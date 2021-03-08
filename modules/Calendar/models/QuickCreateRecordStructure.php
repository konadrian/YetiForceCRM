<?php
/**
 * Calendar quick create record structure file.
 *
 * @package   Model
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Adrian Koń <m.krzaczkowski@yetiforce.com>
 */

/**
 * Calendar quick create record structure class.
 */
class Calendar_QuickCreateRecordStructure_Model extends Vtiger_QuickCreateRecordStructure_Model
{
	/** {@inheritdoc} */
	public function getStructure()
	{
		if (!empty($this->structuredValues)) {
			return $this->structuredValues;
		}
		Vtiger_Field_Model::$tabIndexDefaultSeq = 1000;
		$fieldsDependency = \App\FieldsDependency::getByRecordModel('QuickCreate', $this->record);
		$fieldModelList = $this->getModule()->getQuickCreateFields();
		foreach ($fieldModelList as $fieldName => $fieldModel) {
			if ($fieldsDependency['hide']['backend'] && \in_array($fieldName, $fieldsDependency['hide']['backend'])) {
				continue;
			}
			$recordModelFieldValue = $this->record->get($fieldName);
			if ('date_start' === $fieldName) {
				$fieldModel->set('fieldvalue', $recordModelFieldValue . ' ' . $this->record->get('time_start'));
			} elseif ('due_date' === $fieldName) {
				$fieldModel->set('fieldvalue', $recordModelFieldValue . ' ' . $this->record->get('time_end'));
			} elseif (!empty($recordModelFieldValue)) {
				$fieldModel->set('fieldvalue', $recordModelFieldValue);
			} elseif ('activitystatus' === $fieldName) {
				$currentUserModel = Users_Record_Model::getCurrentUserModel();
				$defaulteventstatus = $currentUserModel->get('defaulteventstatus');
				$fieldValue = $defaulteventstatus;
				$fieldModel->set('fieldvalue', $fieldValue);
			} elseif ('activitytype' === $fieldName) {
				$currentUserModel = Users_Record_Model::getCurrentUserModel();
				$defaultactivitytype = $currentUserModel->get('defaultactivitytype');
				$fieldValue = $defaultactivitytype;
				$fieldModel->set('fieldvalue', $fieldValue);
			} else {
				$defaultValue = $fieldModel->getDefaultFieldValue();
				if ($defaultValue) {
					$fieldModel->set('fieldvalue', $defaultValue);
				}
			}
			if ($fieldModel->get('tabindex') > Vtiger_Field_Model::$tabIndexLastSeq) {
				Vtiger_Field_Model::$tabIndexLastSeq = $fieldModel->get('tabindex');
			}
			if ($fieldsDependency['hide']['frontend'] && \in_array($fieldName, $fieldsDependency['hide']['frontend'])) {
				$fieldModel->set('hideField', true);
			}
			if ($fieldsDependency['mandatory'] && \in_array($fieldName, $fieldsDependency['mandatory'])) {
				$fieldModel->set('isMandatory', true);
			}
			$this->structuredValues[$fieldName] = $fieldModel;
		}
		++Vtiger_Field_Model::$tabIndexLastSeq;
		return $this->structuredValues;
	}
}
