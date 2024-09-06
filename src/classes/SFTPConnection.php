<?php

class SFTPConnection {

    private $connection;
    private $sftp;


    public function __construct($host, $port = 22) {

        $this->connection = @ssh2_connect($host, $port);

        if (!$this->connection) {
            return false;

        }

    }

    public function login($username, $password) {

        if (!@ssh2_auth_password($this->connection, $username, $password)) {
            return false;
        }

        $this->sftp = @ssh2_sftp($this->connection);

        if (!$this->sftp) {
            return false;
        }

    }

    public function uploadFile($local_file, $remote_file) {

        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');

        if (!$stream) {
            return false;
        }

        $data_to_send = @file_get_contents($local_file);

        if ($data_to_send === false) {
            return false;
        }

        if (@fwrite($stream, $data_to_send) === false) {
            return false;
        }

        @fclose($stream);
    }

    function scanFilesystem($remote_file) {

        $sftp = $this->sftp;
        $dir = "ssh2.sftp://$sftp$remote_file";
        $tempArray = [];

        if (is_dir($dir)) {

            if ($dh = opendir($dir)) {

                while (($file = readdir($dh)) !== false) {
                    $filetype = filetype($dir . $file);

                    if ($filetype == "dir") {
                        $tmp = $this->scanFilesystem($remote_file . $file . "/");

                        foreach ($tmp as $t) {
                            $tempArray[] = $file . "/" . $t;
                        }

                    } else {
                        $tempArray[] = $file;
                    }

                }

                closedir($dh);
            }

        }

        return $tempArray;
    }

    public function receiveFile($remote_file, $local_file) {

        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://$sftp$remote_file", 'r');

        if (!$stream) {
            return false;
        }

        $contents = fread($stream, filesize("ssh2.sftp://$sftp$remote_file"));
        file_put_contents($local_file, $contents);
        @fclose($stream);
    }

    public function deleteFile($remote_file) {

        $sftp = $this->sftp;
        unlink("ssh2.sftp://$sftp$remote_file");
    }


}
