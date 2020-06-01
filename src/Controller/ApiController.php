<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AdminControlPanel;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiController extends AbstractController
{
    public function getImg(KernelInterface $kernel, Request $request)
    {
        $userId = $request->query->getInt('user_id');
        if (null === $userId) {
            return $this->json(['error' => true, 'error_message' => 'userid_not_given']);
        }

        $entityManager = $this->getDoctrine()->getManager();
        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->find($userId);
        if (null === $user) {
            return $this->json(['error' => true, 'error_message' => 'user_not_found']);
        }

        $requestedWidth = $request->query->getInt('width', 1000);
        $requestedHeight = $request->query->getInt('height', 1000);
        $rootPictureDir = $kernel->getProjectDir().'/var/data/profile_pictures';
        $pictureName = $user->getProfile()->getPicture();

        $imagine = new Imagine();
        if (null !== $pictureName && file_exists($fileName = $rootPictureDir.'/'.$pictureName)) {
            $image = $imagine->open($fileName);
        } else {
            $image = $imagine->open($kernel->getProjectDir().'/public/img/user.jpg');
        }
        $boxSize = $image->getSize()->getHeight() > $image->getSize()->getWidth() ? $image->getSize()->getHeight(
        ) : $image->getSize()->getWidth();

        $collage = $imagine->create(new Box($boxSize, $boxSize));

        $x = ($boxSize - $image->getSize()->getWidth()) / 2;
        $y = ($boxSize - $image->getSize()->getHeight()) / 2;
        $collage->paste($image, new Point($x, $y));

        $collage->resize(new Box($requestedWidth, $requestedHeight));

        $collage->show('jpg');
        exit;
    }

    public function updateProfilePic(KernelInterface $kernel, Request $request)
    {
        // Method has to be post (So that an image can be sent)
        if ('POST' !== $request->getMethod()) {
            throw $this->createNotFoundException('Has to be POST method');
        }

        // Find user
        $entityManager = $this->getDoctrine()->getManager();
        /** @var User|null $user */
        $user = $entityManager->getRepository(User::class)->find($request->query->getInt('user_id'));
        if (null === $user) {
            throw $this->createNotFoundException('User not found');
        }

        // Check whether an image was sent
        /** @var UploadedFile $file */
        $file = $request->files->get('avatar');
        if (null === $file) {
            throw $this->createNotFoundException('No file has been sent');
        }

        // Validates if the file is in the right format
        if (!in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/gif'], true)) {
            return $this->json(['success' => false, 'error' => 'mine_type_not_valid']);
        }

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // Prepare the directory
        $directory = $kernel->getProjectDir().'/var/data/profile_pictures';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Remove old picture
        $oldPictureName = $user->getProfile()->getPicture();
        $oldPicture = realpath($directory.'/'.$oldPictureName);
        if ((null !== $oldPictureName || (is_string($oldPictureName) && $oldPictureName > 0)) && file_exists(
                $oldPicture
            ) && is_writable($oldPicture)) {
            unlink($oldPicture);
        }

        // Move the file
        $file->move($directory, $fileName);

        // Update db with new picture
        $user->getProfile()->setPicture($fileName);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    public function uploadProgress(Request $request)
    {
        // Assuming default values for session.upload_progress.prefix
        // and session.upload_progress.name:
        $session = $this->get('session');

        $s = $session->get('upload_progress_'.(int) ($request->query->get('PHP_SESSION_UPLOAD_PROGRESS')));
        $progress = [
            'lengthComputable' => true,
            'loaded' => $s['bytes_processed'],
            'total' => $s['content_length'],
        ];

        return $this->json($progress);
    }

    public function panelPages(Request $request, TokenStorageInterface $tokenStorage)
    {
        $page = $request->query->get('p');

        AdminControlPanel::loadLibs($this->get('kernel')->getProjectDir(), $tokenStorage);

        $view = 'AdminDefault::notFound';

        $list = AdminControlPanel::getFlatTree();

        $key = null;
        while ($item = current($list)) {
            if (isset($item['href']) && $item['href'] === $page) {
                $key = key($list);
            }
            next($list);
        }

        if (null !== $key) {
            if (is_callable('\\App\\Controller\\Panel\\'.$list[$key]['view'])) {
                $view = $list[$key]['view'];
            }
        }

        return $this->forward(
            'App\\Controller\\Panel\\'.$view,
            [
                'request' => $request,
            ]
        );
    }

    public function updateUserData(Request $request)
    {
        $element = $request->query->get('element', null);
        $csrf = $request->request->get('csrf', null);

        if (null === $element) {
            throw $this->createNotFoundException();
        }
        if (null === $csrf) {
            throw $this->createAccessDeniedException();
        }

        if ('username' === $element) {
            if (!$this->isCsrfTokenValid('edit_username', $csrf)) {
                throw $this->createAccessDeniedException();
            }
            if (!$request->request->has('username')) {
                throw $this->createNotFoundException();
            }
            /** @var User|null $user */
            $user = $this->getUser();

            $user->setUsername($request->request->get('username'));
            $this->getDoctrine()->getManager()->flush();

            return $this->json(['username_updated']);
        } elseif ('email' === $element) {
            if (!$this->isCsrfTokenValid('edit_email', $csrf)) {
                throw $this->createAccessDeniedException();
            }
            if (!$request->request->has('email')) {
                throw $this->createNotFoundException();
            }
            /** @var User|null $user */
            $user = $this->getUser();

            $user->setEmail($request->request->get('email'));
            $user->setEmailVerified(false);
            $this->getDoctrine()->getManager()->flush();

            return $this->json(['email_updated']);
        }

        return $this->createNotFoundException();
    }
}
