<?php
/**
 *
 */
class PcClassExistsValidator extends CValidator {
	/* @var bool $allowEmpty Whether the attribute is allowed to be empty. */
	public $allowEmpty = false;

	/* @var string $emptyMessage the message to be displayed if an empty value is validated while 'allowEmpty' is false */
	public $emptyMessage = "{attribute} cannot be blank";
	public $errorMessage = " is not a valid class name. Make sure to configure config/main.php 'import' section to load all relevant classes.";

	public function validateAttribute($object, $attribute) {
		// first, if 'allowEmpty' is true and the attribute is indeed empty - finish execution - all good!
		if (empty($object->$attribute)) {
			if ($this->allowEmpty) {
				return;
			}
			$translated_msg = Yii::t("ext", $this->emptyMessage, array('{attribute}' => $attribute));
			$this->addError($object, $attribute, $translated_msg);
			Yii::log("Error: attribute $attribute in model class " . get_class($object) . " cannot be empty, according to validation rules.", CLogger::LEVEL_ERROR, __METHOD__);
			return;
		}

		// now validate that the class exists
		$class_name = $object->$attribute;
		if (! @class_exists($class_name)) {
			$translated_msg = $object->$attribute . Yii::t("ext", $this->errorMessage);
			$this->addError($object, $attribute, $translated_msg);
			// the above message will not be seen anywhere if the validation is not related to some form (such as in the
			// case of AJAX call that goes behind the scenes. Therefore, log this incident as well
			Yii::log("Error: $translated_msg", CLogger::LEVEL_WARNING, __METHOD__);
			return;
		}

	}
}