<html>
    <head>
        <meta charset="UTF-8">
        <title>File Browser</title>
        <link rel="stylesheet" href="css/styles.css"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="js/actionsOnFiles.js"></script>
    </head>
    <body>
        <div class="browser-container">
            <div class="browser-header">
                <div class="disk-changer">
                    <select id="choose-disk">
                        <option value="C">Disk C:</option>
                        <option value="D">Disk D:</option>
                        <option value="E">Disk E:</option>
                        <option value="F">Disk F:</option>
                        <option value="G">Disk G:</option>
                        <option value="H">Disk H:</option>
                        <option value="I">Disk I:</option>
                    </select>
                </div>
                <div class="action-on-files">
                    <img src="graphic/backward.png" id="backward" alt="backward" title="backward"/>
                    <img src="graphic/forward.png" id="forward" alt="forward" title="forward"/>
                    <img src="graphic/delete.png" id="delete" alt="delete" title="delete"/>
                    <img src="graphic/copy.png" id="copy" alt="copy_file" title="copy file"/>
                    <img src="graphic/cut.png" id="cut" alt="cut_file" title="cut file"/>
                    <form class="confirmation-icon" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="submit" name="confirm_action" title="confirm action" value="">
                    </form>
                    <img src="graphic/add-file.png" id="add-file" alt="add_file" title="new file"/>
                    <img src="graphic/add-folder.png" id="add-folder" alt="add_folder" title="new folder"/>
                    <img src="graphic/file-name.png" id="change-name" alt="change_name" title="change name"/>
                    <form class="edit-icon" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<!--                        <img src="graphic/edit-file.png" id="edit-file" alt="edit_file" title="edit file"/>-->
                        <input type="submit" name="edit-file" id="edit-file" alt="edit_file" title="edit file">
                    </form>
                </div>
                <div class="sort-files">
                    <select id="sort-type">
                        <option value="Asc">Name A-Z</option>
                        <option value="Desc">Name Z-A</option>
                        <option value="Date">Date</option>
                        <option value="Type">Type</option>
                    </select>
                </div>
            </div>
            <div class="file-container">

                <?php
                include_once './php/submitForm.php';
                ?>

                <!-- Window conrirmation of removing file/folder -->
                <form class="remove-confirmation" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="submit" name="delete" value="Accept">
                    <input type="submit" name="not_delete" value="Cancel">
                    <label>Do you want to delete this file?</label>
                </form>

                <!-- WIndow for adding files and folders -->
                <form class="add-file-confirmation" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label id="title-fileFolder">Add file name</label>
                    <input type="text" id="name-fileFolder" name="name-fileFolder" placeholder="enter the name...">
                    <input type="submit" id="create-file" name="create-file" value="Create">
                    <input type="submit" name="create-folder" id="create-folder" name="create-folder" value="Create">
                    <input type="submit" name="change-name" id="change-name" value="Change">
                    <input type="submit" name="not-file-folder" value="Cancel">
                </form>

                <!-- Message appearing when removing, copying and cutting files -->
                <div class="action-message">
                    <p>Copying files. Please wait...</p>
                </div>
            </div>
        </div>

    </body>
</html>
