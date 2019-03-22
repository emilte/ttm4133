<?php

namespace ttm4135\webapp\models;

class User
{
    const INSERT_QUERY = "INSERT INTO users(username, password, email, bio, isadmin) VALUES(?, ?, ?, ?, ?)";
    const UPDATE_QUERY = "UPDATE users SET username=?, password=?, email=?, bio=?, isadmin=? WHERE id=?";
    const DELETE_QUERY = "DELETE FROM users WHERE id=?";
    const FIND_BY_NAME_QUERY = "SELECT * FROM users WHERE username='%s'";
    const FIND_BY_ID_QUERY = "SELECT * FROM users WHERE id='%s'";
    protected $id = null;
    protected $username;
    protected $password;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $isAdmin = 0;

    static $app;


    static function make($id, $username, $password, $email, $bio, $isAdmin ) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $user = new User();
        $user->id = $id;
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->bio = $bio;
        $user->isAdmin = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    function save()
    {
        if ($this->id === null) {
            $query = self::$app->db->prepare(self::INSERT_QUERY);
            $query->execute([
                $this->username,
                $this->password,
                $this->email,
                $this->bio,
                $this->isAdmin
            ]);
        } else {
            $query = self::$app->db->prepare(self::UPDATE_QUERY);
            $query->execute([
                $this->username,
                $this->password,
                $this->email,
                $this->bio,
                $this->isAdmin,
                $this->id
            ]);
        }
    }

    function delete()
    {
        $query = self::$app->db->prepare(self::DELETE_QUERY)->execute([$this->id]);
    }

    function getId()
    {
        return $this->id;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->username = $username;
    }

    function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }
    function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }


    /**
     * Get user in db by userid
     *
     * @param string $userid
     * @return mixed User or null if not found.
     */
    static function findById($userid)
    {
        $query = sprintf(self::FIND_BY_ID_QUERY, $userid);
        $result = self::$app->db->query($query, \PDO::FETCH_ASSOC);
        $row = $result->fetch();

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     */
    static function findByUser($username)
    {
        $query = sprintf(self::FIND_BY_NAME_QUERY, $username);
        $result = self::$app->db->query($query, \PDO::FETCH_ASSOC);
        $row = $result->fetch();

        if($row == false) {
            return null;
        }

        return User::makeFromSql($row);
    }


    static function all()
    {
        $query = "SELECT * FROM users";
        $results = self::$app->db->query($query);

        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            array_push($users, $user);
        }

        return $users;
    }

    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['bio'],
            $row['isadmin']
        );
    }

}


  User::$app = \Slim\Slim::getInstance();
