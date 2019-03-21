<?php

// Use in the “Post-Receive URLs” section of your GitHub repo.

if ( $_POST['payload'] ) {
    shell_exec( 'cd /home/grp43/apache/ && git reset –hard HEAD && git pull' );
}

?>hi
