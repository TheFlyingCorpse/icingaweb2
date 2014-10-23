<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Application\Installation;

use Exception;
use Zend_Config;
use Icinga\Web\Setup\Step;
use Icinga\Application\Config;
use Icinga\Config\PreservingIniWriter;

class ResourceStep extends Step
{
    protected $data;

    protected $error;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function apply()
    {
        $resourceConfig = array();
        if (isset($this->data['dbResourceConfig'])) {
            $dbConfig = $this->data['dbResourceConfig'];
            $resourceName = $dbConfig['name'];
            unset($dbConfig['name']);
            $resourceConfig[$resourceName] = $dbConfig;
        }

        if (isset($this->data['ldapResourceConfig'])) {
            $ldapConfig = $this->data['ldapResourceConfig'];
            $resourceName = $ldapConfig['name'];
            unset($ldapConfig['name']);
            $resourceConfig[$resourceName] = $ldapConfig;
        }

        try {
            $writer = new PreservingIniWriter(array(
                'config'    => new Zend_Config($resourceConfig),
                'filename'  => Config::resolvePath('resources.ini'),
                'filemode'  => octdec($this->data['fileMode'])
            ));
            $writer->write();
        } catch (Exception $e) {
            $this->error = $e;
            return false;
        }

        $this->error = false;
        return true;
    }

    public function getSummary()
    {
        if (isset($this->data['dbResourceConfig']) && isset($this->data['ldapResourceConfig'])) {
            $pageTitle = '<h2>' . t('Resources') . '</h2>';
        } else {
            $pageTitle = '<h2>' . t('Resource') . '</h2>';
        }

        if (isset($this->data['dbResourceConfig'])) {
            $dbTitle = '<h3>' . t('Database') . '</h3>';
            $dbHtml = ''
                . '<table>'
                . '<tbody>'
                . '<tr>'
                . '<td><strong>' . t('Resource Name') . '</strong></td>'
                . '<td>' . $this->data['dbResourceConfig']['name'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Database Type') . '</strong></td>'
                . '<td>' . $this->data['dbResourceConfig']['db'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Host') . '</strong></td>'
                . '<td>' . $this->data['dbResourceConfig']['host'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Port') . '</strong></td>'
                . '<td>' . $this->data['dbResourceConfig']['port'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Database Name') . '</strong></td>'
                . '<td>' . $this->data['dbResourceConfig']['dbname'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Username') . '</strong></td>'
                . '<td>' . $this->data['dbResourceConfig']['username'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Password') . '</strong></td>'
                . '<td>' . str_repeat('*', strlen($this->data['dbResourceConfig']['password'])) . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>';
        }

        if (isset($this->data['ldapResourceConfig'])) {
            $ldapTitle = '<h3>LDAP</h3>';
            $ldapHtml = ''
                . '<table>'
                . '<tbody>'
                . '<tr>'
                . '<td><strong>' . t('Resource Name') . '</strong></td>'
                . '<td>' . $this->data['ldapResourceConfig']['name'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Host') . '</strong></td>'
                . '<td>' . $this->data['ldapResourceConfig']['hostname'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Port') . '</strong></td>'
                . '<td>' . $this->data['ldapResourceConfig']['port'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Root DN') . '</strong></td>'
                . '<td>' . $this->data['ldapResourceConfig']['root_dn'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Bind DN') . '</strong></td>'
                . '<td>' . $this->data['ldapResourceConfig']['bind_dn'] . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td><strong>' . t('Bind Password') . '</strong></td>'
                . '<td>' . str_repeat('*', strlen($this->data['ldapResourceConfig']['bind_pw'])) . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>';
        }

        return $pageTitle . (isset($dbTitle) ? '<div class="topic">' . $dbTitle . $dbHtml . '</div>' : '')
            . (isset($ldapTitle) ? '<div class="topic">' . $ldapTitle . $ldapHtml . '</div>' : '');
    }

    public function getReport()
    {
        if ($this->error === false) {
            $message = t('Resource configuration has been successfully written to: %s');
            return '<p>' . sprintf($message, Config::resolvePath('resources.ini')) . '</p>';
        } elseif ($this->error !== null) {
            $message = t('Resource configuration could not be written to: %s; An error occured:');
            return '<p class="error">' . sprintf($message, Config::resolvePath('resources.ini')) . '</p>'
                . '<p>' . $this->error->getMessage() . '</p>';
        }

        return '';
    }
}
