<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AdminControlPanel;
use App\Service\SimpleImage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function getImg(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $userId = $request->query->getInt('user_id');

        /** @var null|\App\Entity\User $selectedUser */
        $selectedUser = $em->find(User::class, $userId);

        $width = $request->query->has('width') ? $request->query->getInt('width') : 1000;
        $height = $request->query->has('height') ? $request->query->getInt('height') : 1000;

        $rootPictureDir = $this->get('kernel')->getProjectDir().'/var/data/profile_pictures';

        if (!is_null($selectedUser)) {
            $pictureName = $selectedUser->getProfile()->getPicture();
            if (!is_null($pictureName) && file_exists($fileName = $rootPictureDir.'/'.$pictureName)) {
                $image = new SimpleImage($fileName);
                $image->resize($width, $height);
                $image->output();
                exit;
            } else {
                $image = new SimpleImage($this->get('kernel')->getProjectDir().'/public/img/user.jpg');
                $image->resize($width, $height);
                $image->output();
                exit;
            }
        } else {
            return $this->json(['error' => true, 'error_message' => 'user_not_found']);
        }
    }

    public function updateProfilePic(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \App\Entity\User $user */
        $user = $em->find(User::class, $request->query->getInt('user_id'));

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get('files');

        // Validates if the file is in the right format
        if (!in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/gif'])) {
            return $this->json(['error' => true, 'error_message' => 'mine_type_not_valid']);
        }

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        $directory = $this->get('kernel')->getProjectDir().'/var/data/profile_pictures';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Move the file
        $file->move($directory, $fileName);

        // Remove old picture
        $oldPictureName = $user->getProfile()->getPicture();
        $oldPicture = realpath($directory.'/'.$oldPictureName);
        if ((!is_null($oldPictureName) || (is_string($oldPictureName) && $oldPictureName > 0)) && file_exists($oldPicture) && is_writable($oldPicture)) {
            unlink($oldPicture);
        }

        // Update db with new picture
        $user->getProfile()->setPicture($fileName);
        $em->flush();

        return $this->json(['files' => ['name' => $fileName]]);
    }

    public function uploadProgress(Request $request)
    {
        // Assuming default values for session.upload_progress.prefix
        // and session.upload_progress.name:
        $session = $this->get('session');

        $s = $session->get('upload_progress_'.intval($request->query->get('PHP_SESSION_UPLOAD_PROGRESS')));
        $progress = [
            'lengthComputable' => true,
            'loaded'           => $s['bytes_processed'],
            'total'            => $s['content_length'],
        ];

        return $progress;
    }

    public function panelPages(Request $request)
    {
        $page = $request->query->get('p');

        AdminControlPanel::loadLibs($this->get('kernel')->getProjectDir(), $this->container);

        $view = 'AdminDefault::notFound';

        $list = AdminControlPanel::getFlatTree();

        $key = null;
        while ($item = current($list)) {
            if (isset($item['href']) && $item['href'] === $page) {
                $key = key($list);
            }
            next($list);
        }

        if (!is_null($key)) {
            if (is_callable('\\App\\Controler\\Panel\\'.$list[$key]['view'])) {
                $view = $list[$key]['view'];
            }
        }

        $response = $this->forward('App\\Controller\\Panel\\'.$view, [
            'request' => $request,
        ]);
        return $response;
    }

    public function updateUserData(Request $request)
    {
        $element = $request->query->has('element') ? $request->query->get('element') : null;
        $csrf = $request->request->has('csrf') ? $request->request->get('csrf') : null;

        if (is_null($element)) {
            throw $this->createNotFoundException();
        }
        if (is_null($csrf)) {
            throw $this->createAccessDeniedException();
        }

        if ($element === 'username') {
            if (!$this->isCsrfTokenValid('edit_username', $csrf)) {
                throw $this->createAccessDeniedException();
            }
            if (!$request->request->has('username')) {
                throw $this->createNotFoundException();
            }
            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            $user->setUsername($request->request->get('username'));
            $this->getDoctrine()->getManager()->flush();
            return $this->json(['username_updated']);

        } elseif ($element === 'email') {
            if (!$this->isCsrfTokenValid('edit_email', $csrf)) {
                throw $this->createAccessDeniedException();
            }
            if (!$request->request->has('email')) {
                throw $this->createNotFoundException();
            }
            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            $user->setEmail($request->request->get('email'));
            $user->setEmailVerified(false);
            $this->getDoctrine()->getManager()->flush();
            return $this->json(['email_updated']);

        } else {
            return $this->createNotFoundException();
        }
    }
}
