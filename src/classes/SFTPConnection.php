<?php
use phpseclib3\Net\SFTP;

class SFTPConnection {

    public $connection;
    public $sftp;

    public function __construct($host, $port = 22) {

        $this->connection = new SFTP($host);

        if (!$this->connection) {
            return false;

        }

    }

    public function login($username, $password) {

        $this->sftp = $this->connection->login($username, $password);

        if (!$this->sftp) {
            return false;
        }

    }

    public function uploadFile($local_file, $remote_file) {

        if ($this->sftp) {

            if ($this->connection->put($remote_file, $local_file)) {
                return true;
            }

        }

        return false;

    }

    public function createFile($remote_file, $local_file) {

        if ($this->sftp) {
            $path = dirname($remote_file);

            if ($this->connection->is_dir($path)) {
                $this->connection->chdir($path);

                if ($this->connection->put($remote_file, $local_file)) {
                    return true;
                } else {
                    return false;
                }

            } else {

                if ($this->connection->mkdir($path, true)) {

                    if ($this->connection->chdir($path)) {

                        if ($this->connection->put($remote_file, $local_file)) {
                            return true;
                        } else {
                            return false;
                        }

                    }

                }

            }

        }

        return false;

    }

    public function receiveFile($remote_file, $local_file) {

        if ($this->sftp) {

            if ($this->connection->get($remote_file, $local_file)) {
                return true;
            }

        }

        return false;

    }

    public function deleteFile($remote_file) {

        if ($this->sftp) {

            if ($this->connection->delete($filePath)($remote_file)) {
                return true;
            }

        }

        return false;
    }

}
