<?php


namespace Commentsystem;


use Exception;
use PDO;
use RuntimeException;

class Container
{
    private static $instance = null;
    private PDO $db;

    /**
     *  Database data, will be filled after the first ini read
     */
    private bool $db_data_read = false;
    private string $db_data_dbhost = "localhost";
    private string $db_data_dbname = "comment_system";
    private string $db_data_username = "api";
    private string $db_data_password = "supersafeapi";

    /**
     * Private Constructor to create Container Singleton
     *
     * Create a Container object Singleton. This can only be called from getInstance
     * or other factory related functions.
     *
     * @param string $ini_location
     *
     * @access private
     */
    private function __construct(string $ini_location)
    {
        $this->readINI($ini_location);
    }

    /**
     * Read INI file
     *
     * @param string $ini_location
     */
    private function readINI(string $ini_location = "container.ini"): void
    {
        if ($this->db_data_read) {
            return;
        }

        // Parse the configuration file
        $ini = parse_ini_file($ini_location, true);

        if (!$ini) {
            die("The configuration file container.ini does not exist. Use the container.ini.template file in directory /config to configure this API.");
        }

        $this->setConfig($ini);
    }

    /**
     * Warning: only call this when you're sure no connections are made yet, otherwise this object
     * will return the objects created with the previous configuration.
     *
     * @param $config
     */
    public function setConfig(array $config): void
    {
        if (isset($config["database"]["hostname"])) {
            $this->db_data_dbhost = $config["database"]["hostname"];
        }

        if (isset($config["database"]["name"])) {
            $this->db_data_dbname = $config["database"]["name"];
        }

        if (isset($config["database"]["username"])) {
            $this->db_data_username = $config["database"]["username"];
        }

        if (isset($config["database"]["password"])) {
            $this->db_data_password = $config["database"]["password"];
        }

        // By using the ini_bag there is no need for a dedicated getter for each and every config item
        $this->ini_bag = $config;

        $this->db_data_read = true;
    }

    /**
     * Connect Database
     *
     * Create a PDO connection and set the connection.
     *
     * @see http://www.php.net/PDO
     */
    public function connectDatabase($database_host = null, $database_name = null)
    {
        try {
            if ($database_name === null) {
                $database_name = $this->db_data_dbname;
            }

            if ($database_host === null) {
                $database_host = $this->db_data_dbhost;
            }

            $pdo_attr = [
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8;",
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ];

            // Suppress the Warning: Could not connect... stale persistent connection
            @$this->db = new PDO("mysql:host=" . $database_host . ";dbname=" . $database_name . ";charset=utf8", $this->db_data_username, $this->db_data_password, $pdo_attr);

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->db;
        } catch (Exception $e) {
            throw new RuntimeException("Database connect failed: " . $e->getMessage());
        }
    }

    /**
     * Get Instance
     *
     * Returns the Container singleton instance
     *
     * @param string $ini_location
     *
     * @return Container Container singleton
     */
    public static function getInstance($ini_location = "/code/config/container.ini")
    {
        if (self::$instance === null && !self::$instance instanceof self) {
            self::$instance = new self($ini_location);
        }

        return self::$instance;
    }

    /**
     * Get Database
     *
     * Returns the PDO database connection
     *
     * @param string $db_host Database host
     * @param string $db_name Database name
     *
     * @return PDO database connection
     * @throws Exception
     */
    public function getDatabase($db_host = null, $db_name = null): PDO
    {
        // If no host is supplied, use default host as supplied in the ini
        if ($db_host === null) {
            $db_host = $this->db_data_dbhost;
        }

        // If no database name is supplied, use the default db name as supplied in the ini
        if ($db_name === null) {
            $db_name = $this->db_data_dbname;
        }
        if (!isset($this->db)) {
            $this->connectDatabase($db_host, $db_name);
        }

        return $this->db;
    }

}