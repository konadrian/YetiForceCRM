<?php
/**
 * RestApi container - Get elements of menu file.
 *
 * @package API
 *
 * @copyright YetiForce Sp. z o.o
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Tomasz Kur <t.kur@yetiforce.com>
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Api\RestApi\BaseAction;

use OpenApi\Annotations as OA;

/**
 * RestApi container - Get elements of menu class.
 *
 * @OA\Info(
 * 		title="YetiForce API for Webservice App. Type: RestApi",
 * 		description="Skip the `/webservice` fragment for connections via ApiProxy. There are two ways to connect to API, with or without rewrite, below are examples of both:
 * rewrite
 * - __CRM_URL__/webservice/RestApi/Users/Login
 * - __CRM_URL__/webservice/RestApi/Accounts/RecordRelatedList/117/Contacts
 * without rewrite
 * - __CRM_URL__/webservice.php?_container=RestApi&module=Users&action=Login
 * - __CRM_URL__/webservice.php?_container=RestApi&module=Accounts&action=RecordRelatedList&record=117&param=Contacts",
 * 		version="0.2",
 * 		termsOfService="https://yetiforce.com/",
 *   	@OA\Contact(
 *     		email="devs@yetiforce.com",
 *     		name="Devs API Team",
 *     		url="https://yetiforce.com/"
 *   	),
 *   	@OA\License(
 *    		name="YetiForce Public License v3",
 *     		url="https://yetiforce.com/en/yetiforce/license"
 *   	),
 * )
 * @OA\Server(
 *		url="https://gitdeveloper.yetiforce.com",
 *		description="URL address for the developer demo version",
 * )
 * @OA\Server(
 *		url="https://gitstable.yetiforce.com",
 *		description="URL address for the latest stable demo version",
 * )
 */
class Files extends \Api\Core\BaseAction
{
	/** {@inheritdoc}  */
	public $allowedMethod = ['PUT'];

	/** {@inheritdoc}  */
	public $responseType = 'file';

	/**
	 * Put method.
	 *
	 * @throws \Api\Core\Exception
	 *
	 * @return \App\Fields\File
	 * @OA\Put(
	 *		path="/webservice/RestApi/Files",
	 *		description="Download files from the system",
	 *		summary="Download files",
	 *		tags={"BaseAction"},
	 *		security={
	 *			{"basicAuth" : {}, "ApiKeyAuth" : {}, "token" : {}}
	 *    	},
	 *		@OA\RequestBody(
	 *  		required=true,
	 *			description="Action parameters to download the file",
	 *			@OA\JsonContent(ref="#/components/schemas/BaseAction_Files_Request"),
	 *			@OA\XmlContent(ref="#/components/schemas/BaseAction_Files_Request"),
	 *	  	),
	 *		@OA\Parameter(
	 *			name="X-ENCRYPTED",
	 *			in="header",
	 *			required=true,
	 *			@OA\Schema(ref="#/components/schemas/Header-Encrypted")
	 *		),
	 *		@OA\Response(
	 *			response=200,
	 *			description="File content, mediaType is dynamic depending on the type of file being downloaded",
	 * 			@OA\MediaType(
	 *				mediaType="application/octet-stream",
	 * 				@OA\Schema(
	 *					type="string",
	 *					format="binary"
	 *				)
	 * 			)
	 *		),
	 *		@OA\Response(
	 *			response=403,
	 *			description="No permissions",
	 *			@OA\JsonContent(ref="#/components/schemas/Exception"),
	 *			@OA\XmlContent(ref="#/components/schemas/Exception"),
	 *		),
	 *		@OA\Response(
	 *			response=404,
	 *			description="File not found",
	 *			@OA\JsonContent(ref="#/components/schemas/Exception"),
	 *			@OA\XmlContent(ref="#/components/schemas/Exception"),
	 *		),
	 *		@OA\Response(
	 *			response=405,
	 *			description="Invalid method",
	 *			@OA\JsonContent(ref="#/components/schemas/Exception"),
	 *			@OA\XmlContent(ref="#/components/schemas/Exception"),
	 *		),
	 *		@OA\Response(
	 *			response=406,
	 *			description="Not Acceptable",
	 *			@OA\JsonContent(ref="#/components/schemas/Exception"),
	 *			@OA\XmlContent(ref="#/components/schemas/Exception"),
	 *		),
	 * ),
	 * @OA\Schema(
	 * 		schema="BaseAction_Files_Request",
	 * 		title="Base action - Files request schema",
	 * 		description="Action parameters to download the file",
	 *		type="object",
	 *		@OA\Property(property="module", type="string", example="Documents"),
	 *		@OA\Property(property="actionName", type="string", example="DownloadFile"),
	 *		@OA\Property(property="record", type="integer", example=1111),
	 *		@OA\Property(property="fileid", type="integer", example=333),
	 * ),
	 */
	public function put()
	{
		$moduleName = $this->controller->request->getModule();
		$action = $this->controller->request->getByType('actionName', 1);
		if (!$moduleName || !$action) {
			throw new \Api\Core\Exception('Invalid method', 405);
		}
		\App\Process::$processName = $action;
		\App\Process::$processType = 'File';
		$handlerClass = \Vtiger_Loader::getComponentClassName('File', $action, $moduleName);
		$handler = new $handlerClass();
		if ($handler) {
			if (!$handler->getCheckPermission($this->controller->request)) {
				throw new \Api\Core\Exception('No permissions', 403);
			}
			return $handler->api($this->controller->request);
		}
		throw new \Api\Core\Exception('Invalid method', 405);
	}
}
