<?php
/**
 * Magento session manager
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ravedigital\CoreUpdate\Session;

use Magento\Framework\Session\Config\ConfigInterface;

/**
 * Session Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class SessionManager extends \Magento\Framework\Session\SessionManager
{

    /**
     * Configure session handler and start session
     *
     * @throws \Magento\Framework\Exception\SessionException
     * @return $this
     */
    public function start()
    {
        if ($this->sessionStartChecker->check()) {
            if (!$this->isSessionExists()) {
                \Magento\Framework\Profiler::start('session_start');

                try {
                    $this->appState->getAreaCode();
                    // @todo MC-18221 need to fix check false positive
                    // phpcs:ignore Magento2.Exceptions.ThrowCatch
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    throw new \Magento\Framework\Exception\SessionException(
                        new \Magento\Framework\Phrase(
                            'Area code not set: Area code must be set before starting a session.'
                        ),
                        $e
                    );
                }

                // Need to apply the config options so they can be ready by session_start
                $this->initIniOptions();
                $this->registerSaveHandler();
                if (isset($_SESSION['new_session_id'])) {
                    // Not fully expired yet. Could be lost cookie by unstable network.
                    session_commit();
                    session_id($_SESSION['new_session_id']);
                }
                $sid = $this->sidResolver->getSid($this);
                // potential custom logic for session id (ex. switching between hosts)
                $this->setSessionId($this->sidResolver->getSid($this));
                session_start();
                if (isset($_SESSION['destroyed'])
                    && $_SESSION['destroyed'] < time() - $this->sessionConfig->getCookieLifetime()
                ) {
                    $this->destroy(['clear_storage' => true]);
                }

                if (isset($_SESSION['destroyed'])) {
                    if ($_SESSION['destroyed'] < time()-300) {
                        $this->destroy(['clear_storage' => true]);
                       
                    }
                }

                $this->validator->validate($this);
                //$this->renewCookie($sid);

                register_shutdown_function([$this, 'writeClose']);

                $this->_addHost();
                \Magento\Framework\Profiler::stop('session_start');
            }
            $this->storage->init(isset($_SESSION) ? $_SESSION : []);
        }
        return $this;
    }

    /**
     * Renew session id and update session cookie
     *
     * @return $this
     */
    /**
     * Performs ini_set for all of the config options so they can be read by session_start
     *
     * @return void
     */
    private function initIniOptions()
    {
        $result = @ini_set('session.use_only_cookies', '1');
        if ($result === false) {
            $error = error_get_last();
            throw new \InvalidArgumentException(
                sprintf('Failed to set ini option session.use_only_cookies to value 1. %s', $error['message'])
            );
        }

        foreach ($this->sessionConfig->getOptions() as $option => $value) {
            if ($option=='session.save_handler') {
                continue;
            } else {
                $result = ini_set($option, $value);
                if ($result === false) {
                    $error = error_get_last();
                    throw new \InvalidArgumentException(
                        sprintf('Failed to set ini option "%s" to value "%s". %s', $option, $value, $error['message'])
                    );
                }
            }
        }
    }
}
