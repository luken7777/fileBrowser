//Global variables
var d = new Date();
//Array that hold the marked folders and files
$foldersMarked = [];

$(document).ready(function () {
    //Disable default right click button on browser
    $(document).bind("contextmenu", function (e) {
        return false;
    });

    //Condition that deals with form submission and prevents from resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    //Function that mark and unmark file/folders
    activeClick($foldersMarked);
    //Move backward
    goBack();
    //Move forward
    goForward();
    //Choose disk from the available list
    chooseDisk();
    //Sorting files
    sortFiles();
    //Popping windows or appearing icons once action is being taken on file
    actionConfirmation();
    //Double click that change path based on what folder being clicked twice
    doubleClick();
});
//Function that checks if folder is clicked or not and takes actions based on that
function activeClick($foldersMarked) {
    //Access to file
    $file = $(".file-container img");

    if ($file.click(function (e) {
        //If ctrl key is being clicked
        if (e.ctrlKey) {
            //If array that contains all the active marked folders is empty
            if ($foldersMarked.length == 0) {
                //Change folder/file image for active state
                if ($(this).attr("alt") == "folder") {
                    $(this).attr('src', "graphic/folder-icon_active.png");
                } else {
                    $(this).attr('src', "graphic/file-icon_active.png");
                }
                $foldersMarked.push($(this));
                //Create cookie that will help us to find folder/file based on its assigned id
                document.cookie = "folder_file_id=" + this.id + ";expires=" + d.getDate() + 3600;
            } else {
                for ($index = 0; $index < $foldersMarked.length; $index++) {
                    //Check if clicked folder is active or not based on currently clicked folder and first index of $foldersMarked array
                    if ($(this) !== $foldersMarked[$index]) {
                        //Whole array checked and there is no sign of previously clicked folders
                        if ($index == $foldersMarked.length - 1) {
                            //Change folder/file image for active state and add its id to $foldersMarked array, then end loop
                            if ($(this).attr("alt") == "folder") {
                                $(this).attr('src', "graphic/folder-icon_active.png");
                            } else {
                                $(this).attr('src', "graphic/file-icon_active.png");
                            }
                            $foldersMarked.push($(this));
                            //Create cookie that will help us to find folder/file based on its assigned id
                            document.cookie = "folder_file_id=" + this.id + ";expires=" + d.getDate() + 3600;
                            break;
                        }
                        //increment to find if clicked folder is stored in next index of $foldersMarked array
                        continue;
                    } else {
                        //Clicked folder has been previously active so change it to its default image and remove from array
                        $(this).attr('src', "graphic/folder-icon.png");
                        $foldersMarked.splice($index, 1);
                        break;
                    }
                }
            }
        } else {
            //Create cookie that will help us to find folder/file based on its assigned id
            document.cookie = "folder_file_id=" + this.id + ";expires=" + d.getDate() + 3600;
            //If ctrl button wasn't pressed change image all the previous clicked folders which
            //were marked either with or without ctrl button to normal 
            for ($index = 0; $index < $foldersMarked.length; $index++) {
                $object = $foldersMarked[$index];
                if ($object.attr("alt") == "folder") {
                    $object.attr('src', 'graphic/folder-icon.png');
                } else {
                    $object.attr('src', 'graphic/file-icon.png');
                }
            }
            //Change the image of clicked file/folder to active and add it to array
            if ($(this).attr("alt") == "folder") {
                $(this).attr('src', "graphic/folder-icon_active.png");
            } else {
                $(this).attr('src', "graphic/file-icon_active.png");
            }
            $foldersMarked.push($(this));
        }
        //Preventing page to load again once the element is clicked
        e.preventDefault();

    }))
        ;
}

function doubleClick($file) {
    //Access to file
    $file = $(".file-container img");
    //Default value of how many times folder being clicked is set to 0
    $timesClicked = 0;
    //Seconds between one click and another
    $secBetweenClicks = [];
    $seconds = 0;
    $previousId = '';
    //If folder is being clicked add 1 to variable that holds the number of clicks
    if ($file.click(function (e) {
        if (!e.ctrlKey) {

            //Assing first clicked id folder or file to variable
            if ($timesClicked == 0) {
                $previousId = this.id;
            }

            //Increment by one number of clicks and set the date when folder/file was clicked
            $timesClicked += 1;
            $secBetweenClicks.push(Date.now());

            //If number of click times equal 2 on the same folder/file clicked twice
            if ($timesClicked == 2) {
                if ($previousId == this.id) {
                    //Measure the time between clicks to decide if action should be taken or not
                    if ($secBetweenClicks[1] - $secBetweenClicks[0] < 1000) {
                        //If clicked object is not a file
                        if ($(this).attr("alt") !== "file") {
                            $previousId = '';
                            //Set cookie with the folder_file_id clicked so we know which folder we should open
                            document.cookie = "double_clicked_id=" + this.id + ";expires=" + d.getDate() + 3600;
                            //Assign value of current directory to the prev_dir_path cookie
                            $dir = getCookie("dir_path");
                            document.cookie = "prev_dir_path=" + $dir + ";expires=" + d.getDate() + 3600;
                            //Refresh page so the cookie with its new value could be added
                            location.reload();
                            //Or if it's a file submit form to edit file in notepad
                        }
                    }
                }
                //Reset click times to 0
                $timesClicked = 0;
                //Empty array
                $secBetweenClicks = [];
            }
        }
    }))
        ;
}

//Move backward when clicked backward button
function goBack() {
    $backwardIcon = $(".action-on-files #backward");
    if ($backwardIcon.click(function (e) {
        $dir = getCookie("dir_path");
        //Create previous dir path based on the current existing path 
        //so we could later use it to go forward to that path by clicking forward button
        document.cookie = "prev_dir_path=" + $dir + ";expires=" + d.getDate() + 3600;
        $dir_lenght = $dir.length - 1;
        //Loop through dir path and cut the last dir while '/' char is found
        for ($i = $dir_lenght; $i > 0; $i--) {
            if ($dir[$i] == '/') {
                $dir = $dir.substring(0, $i);
                break;
            }
        }
        //Set new dir path and reload page
        document.cookie = "dir_path=" + $dir + ";expires=" + d.getDate() + 3600;
        location.reload();
    }))
        ;
}

//Move forward when clicked forward icon
function goForward() {
    $forwardIcon = $(".action-on-files #forward");
    if ($forwardIcon.click(function (e) {
        //Fetch the previous path and assing it as current to the cookie
        $dir = getCookie("prev_dir_path");
        document.cookie = "dir_path=" + $dir + ";expires=" + d.getDate() + 3600;
        location.reload();
    }))
        ;
}

//Choose disk based on select option list
function chooseDisk() {
    $diskSelector = document.getElementById("choose-disk");
    //Fetch the value of select option list and set cookie with a new dir path
    $diskSelector.addEventListener('change', function () {
        var diskLetter = $diskSelector[$diskSelector.selectedIndex].value;
        $dir = diskLetter + ":/";
        document.cookie = "dir_path=" + $dir + ";expires=" + d.getDate() + 3600;
        location.reload();
    });
}

function sortFiles() {
    $sortSelector = document.getElementById("sort-type");
    $sortSelector.addEventListener('change', function () {
        var sortType = $sortSelector[$sortSelector.selectedIndex].value;
        document.cookie = "sort_type=" + sortType + ";expires" + d.getDate() + 3600;
        location.reload();
    })
}

//Function that fetch cookie based on its name
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

//Action confirmation based on what icon being clicked in the header section
function actionConfirmation() {
    //Access to file
    $file = $(".file-container img");

    //Access to buttons on the header of the file browser
    $copyIcon = $(".action-on-files #copy");
    $cutIcon = $(".action-on-files #cut");
    $removeIcon = $(".action-on-files #delete");
    $confirmIcon = $(".confirmation-icon input");
    $folderIcon = $(".action-on-files #add-folder");
    $fileIcon = $(".action-on-files #add-file");
    $changeNameIcon = $(".action-on-files #change-name");
    $editFileIcon = $(".action-on-files #edit-file");

    //Access to hidden windows of the file browser
    $removeWindow = $(".remove-confirmation");
    $fileWindow = $(".add-file-confirmation");

    //Read cookie to find out if it's copy or cut action executed
    $fileAction = getCookie("file_action");
    if ($fileAction) {
        $confirmIcon.css('display', 'block');
    }

    //If folder/file clicked and then proper icon go afterwards show confirmation icon
    //or popping up window
    if ($file.click(function (e) {
        $id = this.id;
        if ($removeIcon.click(function (e) {
            $removeWindow.css("display", "block");
        }))
            ;
        if ($copyIcon.click(function (e) {
            $confirmIcon.css('display', 'block');
            $dir = getCookie("dir_path");
            document.cookie = "path_to_copy=" + $dir + ";expires=" + d.getDate() + 3600;
            document.cookie = "file_action=copy;expires=" + d.getDate() + 3600;
            document.cookie = "copied_id=" + $id + ";expires=" + d.getDate() + 3600;
            //document.cookie = "message_action=Copying files. Please wait...;expires=" + d.getDate() + 3600;
        }))
            ;
        if ($cutIcon.click(function (e) {
            $confirmIcon.css('display', 'block');
            $dir = getCookie("dir_path");
            document.cookie = "path_to_copy=" + $dir + ";expires=" + d.getDate() + 3600;
            document.cookie = "file_action=cut;expires=" + d.getDate() + 3600;
            document.cookie = "copied_id=" + $id + ";expires=" + d.getDate() + 3600;
            //document.cookie = "message_action=Cutting files. Please wait...;expires=" + d.getDate() + 3600;
        }))
            ;
        if ($changeNameIcon.click(function (e) {
            //block other elements if another icon that displays the same window was clicked
            $(".add-file-confirmation #create-file").css('display', 'none');
            $(".add-file-confirmation #create-folder").css('display', 'none');

            //Get an access to change file name window and all its elements
            $fileWindow.css('display', 'block');
            $(".add-file-confirmation #title-fileFolder").html("Change name");
            $(".add-file-confirmation #change-name").css('display', 'block');
        }))
            ;
        if ($editFileIcon.click(function (e) {

        }))
            ;
    }))
        ;
    if ($folderIcon.click(function (e) {
        //block other elements if another icon that displays the same window was clicked
        $(".add-file-confirmation #create-file").css('display', 'none');
        $(".add-file-confirmation #change-name").css('display', 'none');

        //Get an access to folder window and all its elements
        $fileWindow.css('display', 'block');
        $(".add-file-confirmation #title-fileFolder").html("Add folder name");
        $(".add-file-confirmation #create-folder").css('display', 'block');
    }))
        ;
    if ($fileIcon.click(function (e) {
        //block other elements if another icon that displays the same window was clicked
        $(".add-file-confirmation #create-folder").css('display', 'none');
        $(".add-file-confirmation #change-name").css('display', 'none');

        //Get an access to file window and all its elements
        $fileWindow.css('display', 'block');
        $(".add-file-confirmation #title-fileFolder").html("Add file name");
        $(".add-file-confirmation #create-file").css('display', 'block');
    }))
        ;
}

