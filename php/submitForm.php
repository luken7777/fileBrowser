<?php

//Set id to global to have an access to id who's placed in different file
global $id;
//Id that we use to mark our folder position in tree directory
$id = 0;
//Variable we use to check if directory has been created before copying folder to another location
$createdFileFolder = false;
//Default directory
$dir = 'D:/';

//Set default path cookie for file browser
if (!isset($_COOKIE['dir_path'])) {
    //Set new cookie with existing directory path and display all files/folders
    setcookie('dir_path', "$dir", time() + 3600);
    displayTree($id, $dir);
} else {
    $dir = $_COOKIE['dir_path'];
    //If folder is double clicked
    if (isset($_COOKIE['double_clicked_id'])) {
        $doubleClicked = $_COOKIE['double_clicked_id'];
        //Fetch the name of clicked folder
        $fileName = getFileName($dir, $doubleClicked);
        //Add new path directory to current path
        $dir = $dir . "/" . $fileName;
        //Set cookie with new directory path
        setcookie('dir_path', "$dir", time() + 3600);
        //Remove double_clicked_id cookie
        setcookie('double_clicked_id', $doubleClicked, time() - 3600);
    }
    //Show directory path
    displayTree($id, $dir);
}

//If you allow to delete file/folder
if (isset($_POST['delete'])) {
    //If folder/file clicked
    if (isset($_COOKIE['folder_file_id'])) {
        $dir = $_COOKIE['dir_path'];
        $fileToRemove = '';
        $files = scandir($dir);

        //Loop to find the name of the marked file
        for ($index = 0; $index < count($files); $index++) {
            if ($_COOKIE['folder_file_id'] == $index) {
                $fileToRemove = $files[$index];
                break;
            }
        }
        //Whole path directory to remove
        $dirToRemove = $dir . "/" . $fileToRemove;
        //Removal function
        deleteDirectory($dirToRemove);
//        rrmdir($dirToRemove);
        //Refresh page again to accept new cookie value
        header("Refresh: 0");
    }
}

//If confirm action clicked procceed with one of the following action: copy or cut
if (isset($_POST['confirm_action'])) {
    $pathToCopy = $_COOKIE['path_to_copy'];
    $dir = $_COOKIE['dir_path'];
    $id = $_COOKIE['copied_id'];
    $fileName = getFileName($pathToCopy, $id);
    $fileToCopy = $pathToCopy . "/" . "$fileName";

    //Check if action executed on file/folder was copy or cut
    switch ($_COOKIE['file_action']) {
        case "copy":
            recurse_copy($fileToCopy, $dir, $createdFileFolder);
            //Remove cookies
            setcookie('file_action', 'copy', time() - 3600);
            setcookie('folder_file_id', $id, time() - 3600);
            break;
        case "cut":
            //Copy file like in case of pressing copy button
            recurse_copy($fileToCopy, $dir, $createdFileFolder);
            //Remove folder once the file/folder is copied to another location
            rrmdir($fileToCopy);
            //Remove cookies
            setcookie('file_action', 'cut', time() - 3600);
            setcookie('folder_file_id', $id, time() - 3600);
            break;
    }
    //Refresh page
    header("Refresh: 0");
}

//If create button clicked on adding file
if (isset($_POST['create-folder'])) {
    $folderName = $_POST['name-fileFolder'];
    $dir = $_COOKIE['dir_path'];

    if ($folderName) {
        mkdir("$dir/$folderName");
    }
    //Refresh page
    header("Refresh: 0");
}

//If create icon clicked on adding folder
if (isset($_POST['create-file'])) {
    $fileName = $_POST['name-fileFolder'];
    $dir = $_COOKIE['dir_path'];

    if ($fileName) {
        fopen("$dir/$fileName", "w");
    }
    //Refresh page
    header("Refresh: 0");
}

//If change name icon clicked to submit the change of the file name
if (isset($_POST['change-name'])) {
    $newName = $_POST['name-fileFolder'];
    $dir = $_COOKIE['dir_path'];
    $id = $_COOKIE['folder_file_id'];
    $currentName = getFileName($dir, $id);

    //Change file/folder name
    rename("$dir/$currentName", "$dir/$newName");

    //Refesh page
    header("Refresh: 0");
}

//If 
if (isset($_POST['edit-file'])) {
    $id = $_COOKIE['folder_file_id'];
    $dir = $_COOKIE['dir_path'];
    $currentName = getFileName($dir, $id);
    $fileToOpen = $dir . "/" . $currentName;

    $notePath = "C:/Windows/notepad.exe";

    execInBackground($notePath, $fileToOpen);
}

//Display all files and folders in existing path
function displayTree($id, $dir) {
    $files = scandir($dir);
    $numberOfFiles = count($files);
    $fileOrFolder = [];

    if (isset($_COOKIE['sort_type'])) {
        switch ($_COOKIE['sort_type']) {
            case "Asc":
                sort($files);
                break;
            case "Desc":
                rsort($files);
                break;
            case "Date":
                //Empty table that is used to hold dates of modified folder/files
                $modificationDate = [];
                
                //Loop through all the folders/files, fetch the date and write it 
                //in associative array along with file name
                for ($index = 0; $index < $numberOfFiles; $index++) {
                    $fileDate = filemtime($dir . "/" . $files[$index]);
                    $modificationDate[$files[$index]] = $fileDate;
                }

                //Sort array by value
                arsort($modificationDate);
                //Fetch array keys
                $arrayKeys = array_keys($modificationDate);
              
                $files = [];
              
                //Loop through the empty array who's lenght we recorded before
                //and add sorted folder/files to the array
                for ($index = 0; $index < $numberOfFiles; $index++) {
                    $files[$index] = $arrayKeys[$index];
                }
                
                break;
            case "Type": {

                    $indexToChange = 0;

                    for ($index = 0; $index < $numberOfFiles; $index++) {
                        if (!is_dir($dir . "/" . $files[$index])) {
                            //IndexToChange set to 0 as default; If file index position in files array is not the same 
                            //as given IndexToChange position it means that this position belongs to folder
                            if ($indexToChange !== $index) {
                                //Swap very first folder found in files array with file
                                $file = $files[$index];
                                $folder = $files[$indexToChange];
                                $files[$indexToChange] = $file;
                                $files[$index] = $folder;
                            }
                            $indexToChange++;
                        }
                    }
                }
                break;
            case "Size":

                break;
        }
    }

    if ($files) {
        foreach ($files as $f) {
            if (is_dir($dir . "/" . $f)) {
                echo "<img src=graphic/folder-icon.png id=$id alt='folder'/>$f<br>";
            } else {
                echo "<img src=graphic/file-icon.png id=$id alt='file'/>$f<br/>";
            }
            $id++;
        }
    }
}

//Delete file and folders
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

//Copy files/folders
function recurse_copy($src, $dst, $createdFileFolder) {
    //Empty variable that holds folder or file name, that's why we used $fName to name this variable
    $fName = '';

    //Loop through copied path and extract the last part of it, which means cut the part of path until '/'
    if (!$createdFileFolder) {
        for ($index = strlen($src) - 1; $index >= 0; $index--) {
            if ($src[$index] === '/') {
                $fName = substr($src, $index + 1);
                $createdFileFolder = true;
                break;
            }
        }
        //Add extracted part of copied path to destination variable
        $dst = $dst . '/' . $fName;
    }

    //If copied path is dir then open it up, create folder you want to copied data into
    //and then proceed with copying data
    if (is_dir($src)) {
        $dir = opendir($src);
        @mkdir($dst);

        //Loop through given path and copy all file and folders
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file, $dst . '/' . $file, $createdFileFolder);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    } else {
        //Copying file
        copy($src, $dst);
    }
}

//Get file name based on current path and id assigned to this file
function getFileName($dir, $id) {
    if ($id) {
        $fileName = '';
        $files = scandir($dir);

        //Loop to find the name of the clicked file
        for ($index = 0; $index < count($files); $index++) {
            //If cookie value is the same as file index
            //then fetch the file name
            if ($id == $index) {
                $fileName = $files[$index];
                //Remove cookie after folder/file id has been found
                setcookie($id, '', time() - 3600);
                break;
            }
        }
        //Return file name from the pile of files 
        return $fileName;
    }
}

//Execute windows app in background
function execInBackground($cmd, $fileName) {
    if (substr(php_uname(), 0, 7) == "Windows") {
        (pclose(popen("start /B " . $cmd . " " . $fileName, "r")));
    } else {
        exec($cmd . " > /dev/null &");
    }
}

?>
