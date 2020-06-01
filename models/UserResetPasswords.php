<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Translations as Trans;

/**
 * ResetPasswords
 * Stores the reset password codes and their evolution
 */
class UserResetPasswords extends Model
{
    /**
     * @var integer
     */
    protected $id;
    /**
     * @var integer
     */
    protected $user_id;
    /**
     * @var string
     */
    protected $code;
    /**
     * @var integer
     */
    protected $created_at;
    /**
     * @var integer
     */
    protected $updated_at;
    /**
     * @var string
     */
    protected $reset;
    /**
     * Before create the user assign a password
     */

    public function initialize()
    {
        $this->belongsTo('user_id', 'Signa\Models\Users', 'id', array('alias' => 'User'));
    }

    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->setCreatedAt(date("Y-m-d H:i:s"));
        // Generate a random confirmation code
        $this->setCode(preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24))));
        // Set status to non-confirmed
        $this->setReset('N');
    }
    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Timestamp the confirmaton
        $this->setUpdatedAt(date("Y-m-d H:i:s"));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param int $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param int $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return string
     */
    public function getReset()
    {
        return $this->reset;
    }

    /**
     * @param string $reset
     */
    public function setReset($reset)
    {
        $this->reset = $reset;
    }
}