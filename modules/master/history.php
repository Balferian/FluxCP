<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$this->redirect($this->url('history', 'index'));
?>