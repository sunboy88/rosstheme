<?php
/**
 * Holds the user information after a successful sign-in.
 */
class Hotmail_Wll_User
{

    /**
    * Initialize the User with time stamp, userid, flags, context and token.
    */
    public function __construct($timestamp, $id, $flags, $context, $token)
    {
        self::setTimestamp($timestamp);
        self::setId($id);
        self::setFlags($flags);
        self::setContext($context);
        self::setToken($token);
    }

    private $_timestamp;
    
    /**
     * Returns the Unix timestamp as obtained from the SSO token.
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * Sets the Unix timestamp.
     */
    private function setTimestamp($timestamp)
    {
        if (!$timestamp) {
            throw new Exception('Error: WLL_User: Null timestamp.');
        }

        if (!preg_match('/^\d+$/', $timestamp) || ($timestamp <= 0)) {
            throw new Exception('Error: WLL_User: Invalid timestamp: ' 
                                . $timestamp);
        }
        
        $this->_timestamp = $timestamp;
    }

    private $_id;

    /**
     * Returns the pairwise unique ID for the user.
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the pairwise unique ID for the user.
     */
    private function setId($id)
    {
        if (!$id) {
            throw new Exception('Error: WLL_User: Null id.');
        }

        if (!preg_match('/^\w+$/', $id)) {
            throw new Exception('Error: WLL_User: Invalid id: ' . $id);
        }
        
        $this->_id = $id;
    }

    private $_usePersistentCookie;

    /**
     * Indicates whether the application is expected to store the
     * user token in a session or persistent cookie.
     */
    public function usePersistentCookie() 
    {
        return $this->_usePersistentCookie;
    }

    /**
     * Sets the usePersistentCookie flag for the user.
     */
    private function setFlags($flags)
    {
        $this->_usePersistentCookie = false;
        if (preg_match('/^\d+$/', $flags)) {
            $this->_usePersistentCookie = (($flags % 2) == 1);
        }
    }

    private $_context;
    
    /** 
     * Returns the application context that was originally passed
     * to the sign-in request, if any.
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Sets the the Application context.
     */
    private function setContext($context)
    {
        $this->_context = $context;
    }

    private $_token;

    /**
     * Returns the encrypted Web Authentication token containing 
     * the UID. This can be cached in a cookie and the UID can be
     * retrieved by calling the ProcessToken method.
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Sets the the User token.
     */
    private function setToken($token)
    {
        $this->_token = $token;
    }
}
