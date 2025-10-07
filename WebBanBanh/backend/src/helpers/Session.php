<?php
/**
 * Session Helper Class
 * Quản lý session cho website
 */

class Session {

    /**
     * Khởi tạo session
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set session
     */
    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session
     */
    public static function get($key, $default = null) {
        self::init();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Check if session exists
     */
    public static function has($key) {
        self::init();
        return isset($_SESSION[$key]);
    }

    /**
     * Delete session
     */
    public static function delete($key) {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy all sessions
     */
    public static function destroy() {
        self::init();
        session_destroy();
    }

    /**
     * Set flash message
     */
    public static function setFlash($type, $message) {
        self::set('flash_' . $type, $message);
    }

    /**
     * Get and delete flash message
     */
    public static function getFlash($type) {
        $message = self::get('flash_' . $type);
        self::delete('flash_' . $type);
        return $message;
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return self::has('user_id');
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::get('user_role') === 'admin';
    }

    /**
     * Get current user ID
     */
    public static function getUserId() {
        return self::get('user_id');
    }

    /**
     * Set user session
     */
    public static function setUser($user) {
        self::set('user_id', $user['id']);
        self::set('user_name', $user['name']);
        self::set('user_email', $user['email']);
        self::set('user_role', $user['role']);
    }

    /**
     * Clear user session
     */
    public static function clearUser() {
        self::delete('user_id');
        self::delete('user_name');
        self::delete('user_email');
        self::delete('user_role');
    }

    /**
     * Cart functions
     */
    public static function addToCart($productId, $quantity = 1) {
        self::init();
        $cart = self::get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        self::set('cart', $cart);
    }

    public static function updateCart($productId, $quantity) {
        self::init();
        $cart = self::get('cart', []);

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        self::set('cart', $cart);
    }

    public static function removeFromCart($productId) {
        self::init();
        $cart = self::get('cart', []);
        unset($cart[$productId]);
        self::set('cart', $cart);
    }

    public static function getCart() {
        return self::get('cart', []);
    }

    public static function clearCart() {
        self::delete('cart');
    }

    public static function getCartCount() {
        $cart = self::getCart();
        return array_sum($cart);
    }
}
?>