<?php

/**
 * Class Database
 *
 * This class is the class that creates a connection to the database.
 */
class Database
{
    /**
     * @var string $host contains the hostname where the database is located
     */
    private string $host = 'localhost';

    /**
     * @var string $db contains the name of the database
     */
    private string $db = 'secrets';

    /**
     * @var string $uname contains the username that is needed to log in to the database
     */
    private string $uname = 'root';

    /**
     * @var string $db contains the password that is needed to log in to the database
     */
    private string $pw = '';

    /**
     * Connects to the database
     *
     * Using the class attributes, a new PDO is created and returned.
     * If any error occurs, the application stops.
     *
     * @return PDO
     */
    public function connect(): PDO
    {
        try {
            return new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->uname, $this->pw);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
