<?php

class ErrorController extends YafController
{

    public function errorAction($exception)
    {

        $this->_view->disableLayout();
        $this->logger->ERROR(sprintf("uid:%s uri:%s code:%s msg:%s stack:\r\n%s", $this->uid, $this->getRequest()->getRequestUri(), $exception->getCode(), $exception->getMessage(), $exception->getTraceAsString()),
            __FILE__, __LINE__, '404');

        $this->_view->display('error/notfound.phtml');
        return false;
        //1. assign to view engine
    }

    public function notFoundAction()
    {
        $this->_view->disableLayout();
        $this->_view->display('error/404.phtml');
        return false;
    }

}

