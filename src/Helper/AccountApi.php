<?php

namespace App\Helper;

use App\Entity\User;
use Psr\Container\ContainerInterface;

class AccountApi
{
    /**
     * @param string $message
     *
     * @return array
     */
    private static function __send_error_message($message)
    {
        return [
            'error'         => true,
            'error_message' => $message,
        ];
    }

    public static function getImg(ContainerInterface $container)
    {
        $request = $container->get('request_stack')->getCurrentRequest();
        $em = $container->get('doctrine')->getManager();
        $kernel = $container->get('kernel');

        $userId = $request->query->getInt('user_id');

        /** @var null|\App\Entity\User $selectedUser */
        $selectedUser = $em->find(User::class, $userId);

        $width = $request->query->has('width') ? $request->query->getInt('width') : 1000;
        $height = $request->query->has('height') ? $request->query->getInt('height') : 1000;

        $rootPictureDir = $kernel->getProjectDir().'/public/app/profile_pictures';

        if (!is_null($selectedUser)) {
            $pictureName = $selectedUser->getProfile()->getPicture();
            if (!is_null($pictureName) && file_exists($fileName = $rootPictureDir.'/'.$pictureName)) {
                $image = new SimpleImage($fileName);
                $image->resize($width, $height);
                $image->output();
                return null;
            } else {
                $image = new SimpleImage($kernel->getProjectDir().'/public/img/user.jpg');
                $image->resize($width, $height);
                $image->output();
                return null;
            }
        } else {
            return self::__send_error_message('user_not_found');
        }
    }

    public static function updateProfilePic(ContainerInterface $container)
    {
        $request = $container->get('request_stack')->getCurrentRequest();
        $em = $container->get('doctrine')->getManager();
        $kernel = $container->get('kernel');

        /** @var \App\Entity\User $current_user */
        $current_user = $em->find(User::class, $request->query->getInt('user_id'));

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get('files');

        // Validates if the file is in the right format
        if (!in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/gif'])) {
            return self::__send_error_message('mine_type_not_valid');
        }

        // Generate a unique name for the file before saving it
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        $directory = $kernel->getProjectDir().'/public/app/profile_pictures';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Move the file
        $file->move($directory, $fileName);

        // Remove old picture
        $oldPictureName = $current_user->getProfile()->getPicture();
        $oldPicture = realpath($directory.'/'.$oldPictureName);
        if ((!is_null($oldPictureName) || (is_string($oldPictureName) && $oldPictureName > 0)) && file_exists($oldPicture) && is_writable($oldPicture)) {
            unlink($oldPicture);
        }

        // Update db with new picture
        $current_user->getProfile()->setPicture($fileName);
        $em->flush();

        return ['files' => ['name' => $fileName]];
    }

    public static function uploadProgress(ContainerInterface $container)
    {
        // Assuming default values for session.upload_progress.prefix
        // and session.upload_progress.name:
        $request = $container->get('request_stack')->getCurrentRequest();
        $session = $container->get('session');

        $s = $session->get('upload_progress_'.intval($request->query->get('PHP_SESSION_UPLOAD_PROGRESS')));
        $progress = [
            'lengthComputable' => true,
            'loaded'           => $s['bytes_processed'],
            'total'            => $s['content_length'],
        ];

        return $progress;
    }

    public static function panelPages(ContainerInterface $container)
    {
        $request = $container->get('request_stack')->getCurrentRequest();
        $kernel = $container->get('kernel');

        $page = $request->query->get('p');

        AdminControlPanel::loadLibs($kernel->getProjectDir(), $container);

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
            if (is_callable('\\App\\AdminAddons\\'.$list[$key]['view'])) {
                $view = $list[$key]['view'];
            }
        }
        $response = call_user_func('\\App\\AdminAddons\\'.$view, $container);

        return $response;
    }
}
