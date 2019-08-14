<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |
                                                        |_|
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		1.0.10
	@build			14th August, 2019
	@created		24th February, 2016
	@package		Support Groups
	@subpackage		ajax.json.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Support Groups

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Supportgroups Ajax Controller
 */
class SupportgroupsControllerAjax extends JControllerLegacy
{
	public function __construct($config)
	{
		parent::__construct($config);
		// make sure all json stuff are set
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		JResponse::setHeader('Content-Disposition','attachment;filename="getajax.json"');
		JResponse::setHeader("Access-Control-Allow-Origin", "*");
		// load the tasks 
		$this->registerTask('getItemData', 'ajax');
		$this->registerTask('getRows', 'ajax');
		$this->registerTask('getColumns', 'ajax');
		$this->registerTask('getTableTotals', 'ajax');
	}

	public function ajax()
	{
		$user 		= JFactory::getUser();
		$jinput 	= JFactory::getApplication()->input;
		// Check Token!
		$token 		= JSession::getFormToken();
		$call_token	= $jinput->get('token', 0, 'ALNUM');
		if($jinput->get($token, 0, 'ALNUM') || $token === $call_token)
		{
			$task = $this->getTask();
			switch($task)
			{
				case 'getItemData':
					try
					{
						$returnRaw = $jinput->get('raw', false, 'BOOLEAN');
						$idValue = $jinput->get('id', NULL, 'INT');
						$typeValue = $jinput->get('type', NULL, 'WORD');
						$requestViewValue = $jinput->get('requestView', NULL, 'WORD');
						$idValue = $jinput->get('id', NULL, 'INT');
						$typeValue = $jinput->get('type', NULL, 'WORD');
						$requestViewValue = $jinput->get('requestView', NULL, 'WORD');
						if($idValue && $typeValue && $requestViewValue && $idValue && $typeValue && $requestViewValue)
						{
							$result = $this->getModel('ajax')->getItemData($idValue, $typeValue, $requestViewValue, $idValue, $typeValue, $requestViewValue);
						}
						else
						{
							$result = false;
						}
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback."(".json_encode($e).");";
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'getRows':
					try
					{
						$returnRaw = $jinput->get('raw', false, 'BOOLEAN');
						$keyValue = $jinput->get('key', NULL, 'ALNUM');
						$pageValue = $jinput->get('page', NULL, 'WORD');
						if($keyValue && $pageValue)
						{
							$result = $this->getModel('ajax')->getRows($keyValue, $pageValue);
						}
						else
						{
							$result = false;
						}
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback."(".json_encode($e).");";
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'getColumns':
					try
					{
						$returnRaw = $jinput->get('raw', false, 'BOOLEAN');
						$pageValue = $jinput->get('page', NULL, 'WORD');
						if($pageValue)
						{
							$result = $this->getModel('ajax')->getColumns($pageValue);
						}
						else
						{
							$result = false;
						}
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback."(".json_encode($e).");";
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'getTableTotals':
					try
					{
						$returnRaw = $jinput->get('raw', false, 'BOOLEAN');
						$idValue = $jinput->get('id', NULL, 'INT');
						if($idValue)
						{
							$result = $this->getModel('ajax')->getTableTotals($idValue);
						}
						else
						{
							$result = false;
						}
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback = $jinput->get('callback', null, 'CMD'))
						{
							echo $callback."(".json_encode($e).");";
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
			}
		}
		else
		{
			if($callback = $jinput->get('callback', null, 'CMD'))
			{
				echo $callback."(".json_encode(false).");";
			}
			else
  			{
				echo "(".json_encode(false).");";
			}
		}
	}
}
