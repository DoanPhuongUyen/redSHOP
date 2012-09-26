<?php
/**
 * @package     redSHOP
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'core' . DS . 'controller.php';

class newslettersubscr_detailController extends RedshopCoreController
{
    public function __construct($default = array())
    {
        parent::__construct($default);
        $this->registerTask('add', 'edit');
    }

    public function edit()
    {
        $this->input->set('view', 'newslettersubscr_detail');
        $this->input->set('layout', 'default');
        $this->input->set('hidemainmenu', 1);

        $model = $this->getModel('newslettersubscr_detail');

        $userlist = $model->getuserlist();

        //merging select option in the select box
        $temps           = array();
        $temps[0]->value = 0;
        $temps[0]->text  = JText::_('COM_REDSHOP_SELECT');
        $userlist        = array_merge($temps, $userlist);

        $this->input->set('userlist', $userlist);

        parent::display();
    }

    public function apply()
    {
        $this->save(1);
    }

    public function save($apply = 0)
    {
        $post                     = $this->input->getArray($_POST);
        $post["body"]             = $this->input->post->getString('body', '');
        $option                   = $this->input->get('option');
        $cid                      = $this->input->post->get('cid', array(0), 'array');
        $post ['subscription_id'] = $cid [0];

        $model    = $this->getModel('newslettersubscr_detail');
        $userinfo = $model->getUserFromEmail($post['email']);

        if (count($userinfo) > 0)
        {
            $post['email']   = $userinfo->user_email;
            $post['user_id'] = $userinfo->user_id;
        }

        $post ['name'] = $post['username'];
        if ($row = $model->store($post))
        {

            $msg = JText::_('COM_REDSHOP_NEWSLETTER_SUBSCR_DETAIL_SAVED');
        }
        else
        {

            $msg = JText::_('COM_REDSHOP_ERROR_SAVING_NEWSLETTER_SUBSCR_DETAIL');
        }

        if ($apply == 1)
        {
            $this->setRedirect('index.php?option=' . $option . '&view=newslettersubscr_detail&task=edit&cid[]=' . $row->subscription_id, $msg);
        }
        else
        {
            $this->setRedirect('index.php?option=' . $option . '&view=newslettersubscr', $msg);
        }
    }

    public function remove()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JError::raiseError(500, JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_DELETE'));
        }

        $model = $this->getModel('newslettersubscr_detail');

        if (!$model->delete($cid))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }

        $msg = JText::_('COM_REDSHOP_NEWSLETTER_SUBSCR_DETAIL_DELETED_SUCCESSFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=newslettersubscr', $msg);
    }

    public function publish()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JError::raiseError(500, JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_PUBLISH'));
        }

        $model = $this->getModel('newslettersubscr_detail');

        if (!$model->publish($cid, 1))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }

        $msg = JText::_('COM_REDSHOP_NEWSLETTER_SUBSCR_DETAIL_PUBLISHED_SUCCESFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=newslettersubscr', $msg);
    }

    public function unpublish()
    {
        $option = $this->input->get('option');
        $cid    = $this->input->post->get('cid', array(0), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JError::raiseError(500, JText::_('COM_REDSHOP_SELECT_AN_ITEM_TO_UNPUBLISH'));
        }

        $model = $this->getModel('newslettersubscr_detail');

        if (!$model->publish($cid, 0))
        {
            echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
        }

        $msg = JText::_('COM_REDSHOP_NEWSLETTER_SUBSCR_DETAIL_UNPUBLISHED_SUCCESFULLY');
        $this->setRedirect('index.php?option=' . $option . '&view=newslettersubscr', $msg);
    }

    public function cancel()
    {
        $option = $this->input->get('option');

        $msg = JText::_('COM_REDSHOP_NEWSLETTER_SUBSCR_DETAIL_EDITING_CANCELLED');
        $this->setRedirect('index.php?option=' . $option . '&view=newslettersubscr', $msg);
    }

    public function export_data()
    {
        $model = $this->getModel('newslettersubscr_detail');

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: text/x-csv");
        header("Content-type: text/csv");
        header("Content-type: application/csv");
        header('Content-Disposition: attachment; filename=NewsletterSbsc.csv');

        echo "Subscriber Full Name,Newsletter,Email Id\n\n";
        $data = $model->getnewslettersbsc();

        for ($i = 0; $i < count($data); $i++)
        {
            $subname = $model->getuserfullname($data[$i]->user_id);
            echo $fullname = $subname->firstname . " " . $subname->lastname;
            echo ",";
            echo $data[$i]->name . ",";
            echo $subname->email . ",";
            echo "\n";
        }

        exit;
    }

    public function export_acy_data()
    {
        ob_clean();
        $model          = $this->getModel('newslettersubscr_detail');
        $cid            = $this->input->post->get('cid', array(), 'array');
        $order_function = new order_functions();
        $data           = $model->getnewslettersbsc($cid);

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: text/x-csv");
        header("Content-type: text/csv");
        header("Content-type: application/csv");
        header('Content-Disposition: attachment; filename=import_to_acyba.csv');

        echo '"email","name","enabled"';
        echo "\n";

        for ($i = 0; $i < count($data); $i++)
        {

            echo '"' . $data[$i]->email . '","';
            if ($data[$i]->user_id != 0)
            {
                $subname = $order_function->getUserFullname($data[$i]->user_id);
                echo $subname;
            }
            else
            {
                echo $data[$i]->subscribername;
            }
            echo '","';
            echo $data[$i]->published . '"';
            echo "\n";
        }

        exit;
    }
}
