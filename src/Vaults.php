<?php

namespace Devker\Vaults;

class Vaults
{
    public static function convertToJSON($params)
    {
        return json_encode($params, JSON_PRETTY_PRINT);
    }

    public static function convertToArray($params)
    {
        return json_decode($params, true);
    }

    public static function stringEncryptB64($params)
    {
        return base64_encode($params);
    }

    public static function stringDecryptB64($params)
    {
        return base64_decode($params);
    }

    public static function getHostLink($folderPath = "")
    {
        $link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . ltrim($folderPath, "/");
        return rtrim($link, "/");
    }

    public static function redirectPage($pageName)
    {
        $redirectScript = "";
        $redirectScript .= "<script>";
        $redirectScript .= 'window.location.href = \'' . $pageName . '\'';
        $redirectScript .= "</script>";
        echo $redirectScript;
    }

    public static function callAPI($apiMethod = "GET", $apiUrl = "", $data = null, $files = true, $headers = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        if ($headers == null) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, []);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        switch ($apiMethod) {
            case "GET":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POST, true);
                if ($files == true) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                } else {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        $curl_response = curl_exec($curl);
        curl_close($curl);
        return $curl_response;
    }

    public static function encryptDecrypt($action = 'encrypt', $string, $secretKey, $secretIV, $encryptMethod = 'AES-256-CBC', $hashKey = 'sha256')
    {
        $output = false;
        $key = hash($hashKey, $secretKey);
        $iv = substr(hash($hashKey, $secretIV), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encryptMethod, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encryptMethod, $key, 0, $iv);
        } else {
            $output = false;
        }
        return $output;
    }

    public static function getClientIP()
    {
        return getHostByName(getHostName());
    }

    public static function deleteFile($filePath)
    {
        $fileDeleted = '';
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                $fileDeleted = true;
            } else {
                $fileDeleted = -1;
            }
        } else {
            $fileDeleted = false;
        }
        return $fileDeleted;
    }

    public static function emptyDirectory($directoryPath)
    {
        $emptyDirectory = false;
        if (array_map('unlink', glob("$directoryPath/*.*"))) {
            $emptyDirectory = true;
        } else {
            $emptyDirectory = false;
        }
        return $emptyDirectory;
    }

    public static function removeHTMLEntities($input)
    {
        $output = htmlspecialchars($input);
        $output = htmlentities($output);
        return $output;
    }

    public static function resizeImage($resourceType, $image_width, $image_height, $resizeWidth, $resizeHeight)
    {
        $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($imageLayer, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_width, $image_height);
        return $imageLayer;
    }

    public static function display($value)
    {
        print_r($value);
        die();
    }


    public static function fileDetails($file, $fileDetails)
    {
        $details = "";
        $tempName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileName = $file['name'];
        $fileType = $file['type'];

        switch ($fileDetails) {
            case "fileTempName":
                $details = $tempName;
                break;
            case "fileSize":
                $details = $fileSize;
                break;
            case "fileName":
                $details = $fileName;
                break;
            case "fileType":
                $details = $fileType;
                break;
            case "width":
                $sourceProperties = getimagesize($tempName);
                $details = $sourceProperties[0];
                break;
            case "height":
                $sourceProperties = getimagesize($tempName);
                $details = $sourceProperties[1];
                break;
            case "fileExtension":
                $details = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                break;
            case "fileEncrypt":
                $details = base64_encode(file_get_contents($tempName));
                break;
            default:
                $details = "No details found";
                break;
        }
        return $details;
    }


    public static function getEncryptedFileSize($encryptedFile)
    {
        $size = strlen(base64_decode($encryptedFile));
        return $size;
    }

    public static function fileUpload($pathDirectory, $fileTmpName, $extension, $fileName)
    {
        if (!is_dir($pathDirectory)) {
            mkdir($pathDirectory, 0777);
        }
        $fileMoved = move_uploaded_file($fileTmpName, $pathDirectory . $fileName . "." . $extension);

        if($fileMoved){
             return $fileName;
        }else{
            return false;
        }
       
    }
}
