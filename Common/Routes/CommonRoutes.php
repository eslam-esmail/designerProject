<?php

$router->group([
//    'prefix' => 'designerCorner',
        ], function ($router) {

    $router->post('uploader', function() {
        $return = ['status' => false, 'path' => false];
        $request = Illuminate\Http\Request::capture();
        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');
            $filePath = "../designersproject/temp_photos/temp";
            $mimes = ['video/3gpp', 'application/pdf', 'video/x-msvideo', 'image/bmp', 'video/x-flv', 'image/gif', 'image/jpeg', 'image/x-citrix-jpeg', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'video/x-ms-wmv', 'video/mp4', 'application/mp4', 'video/webm', 'image/png', 'image/x-citrix-png', 'image/x-png'];
            $mime = $file->getMimeType();
            $size = getimagesize($file->getPathname()); // array 0 => width   1 => height
            $extension = $file->getClientOriginalExtension();
            if (!in_array($mime, $mimes)) {
                return response(['status' => false, 'path' => false, 'msg' => 'Unsupported file type']);
            }
//            if (!($size[0] >= 700 && $size[0] <= 1000) || !($size[1] >= 700 && $size[1] <= 1000)) {
//                return response(['status' => false, 'path' => false, 'msg' => 'Unsupported file size, should between 700 * 700 to 1000 * 1000']);
//            }
//            if ($file->getSize() > (1 * 1024 * 1024)) {
//                return response(['status' => false, 'path' => false, 'msg' => 'Unsupported file size, should be less than 1MB']);
//            }
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $filename = uniqid() . ".$extension";
            $file->move($filePath, $filename);
            $return = ['status' => TRUE, 'path' => url("temp_photos/temp/$filename")];
        }
        return $return;
    });

    $router->post('multiUploader', function() {
        $images = [];
        $request = Illuminate\Http\Request::capture();
        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');
            $filePath = "../designersproject/temp_photos/temp";
            $mimes = ['video/3gpp', 'application/pdf', 'video/x-msvideo', 'image/bmp', 'video/x-flv', 'image/gif', 'image/jpeg', 'image/x-citrix-jpeg', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'video/x-ms-wmv', 'video/mp4', 'application/mp4', 'video/webm', 'image/png', 'image/x-citrix-png', 'image/x-png'];

            foreach ($file as $key => $value) {
                $mime = $file[$key]->getMimeType();
                $size = getimagesize($file[$key]->getPathname()); // array 0 => width   1 => height
                $extension = $file[$key]->getClientOriginalExtension();
                if (!in_array($mime, $mimes)) {
                    return response(['status' => false, 'path' => false, 'msg' => 'Unsupported file type']);
                }
//                if (!($size[0] >= 700 && $size[0] <= 1000) || !($size[1] >= 700 && $size[1] <= 1000)) {
//                    return response(['status' => false, 'path' => false, 'msg' => 'Unsupported file size, should between 700 * 700 to 1000 * 1000']);
//                }
//                if ($file[$key]->getSize() > (1 * 1024 * 1024)) {
//                    return response(['status' => false, 'path' => false, 'msg' => 'Unsupported file size, should be less than 1MB']);
//                }
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }
                $filename = uniqid() . ".$extension";
                $file[$key]->move($filePath, $filename);
                $itemNum = explode('.', $file[$key]->getClientOriginalName());
                $itemNum = explode('_', $itemNum[0]);
                array_push($images, (object) array('status' => TRUE, 'path' => url("images/temp/$filename"), 'itemNumber' => $itemNum[0]));
//                $return = ['status' => TRUE, 'path' => url("images/temp/$filename")];
            }
        }
        return $images;
    });

    $file_path = base_path('Modules');
    $di = scandir($file_path);
    foreach ($di as $child) {
        $file = base_path("Modules/$child/Routes/". ucfirst($child)."Routes.php");
        if ($child != '.' && $child != '..' && file_exists($file)) {
            require $file;
        }
    }
});

