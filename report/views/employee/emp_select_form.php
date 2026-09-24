<script>
$(document).ready(function () {

   $('.checkall').click(function () {
        $(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
	if(this.checked)
	   $(this).parents("fieldset:eq(0)").find(".checkbox").prop("checked",true);
	else
	   $(this).parents("fieldset:eq(0)").find(".checkbox").prop("checked",false);
    });
});
$(document).ready(function(){
$('.btn.generate').click(function (e) {
  if ($("input[type=checkbox]:checked").length === 0) {
	e.preventDefault();
      alert("<?php echo Yii::t('report', 'Please select atleast one checkbox') ?>");
      return false;
  }
});
});
</script>

<fieldset>
<div style="padding: 5px 5px 5px 19px;">
	<input type="checkbox" class="checkall" id="check_all_id"> <label for="check_all_id"> <?php echo Yii::t('report', 'Check All'); ?> </label>
</div>

<div class="checkbox">
 <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	   <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_unique_id" value="emp_unique_id" /> &nbsp;<?php echo Yii::t('report', 'Employee No'); ?>
 	   </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	   <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_first_name" value="emp_first_name" /> &nbsp;<?php echo Yii::t('report', 'First Name'); ?></label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	   <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]"  id="emp_middle_name" value="emp_middle_name" /> &nbsp;<?php echo Yii::t('report', 'Middle Name'); ?></label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	   <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_last_name" value="emp_last_name" /> &nbsp;<?php echo Yii::t('report', 'Last Name'); ?> </label>
	</div>

 </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_mother_name" value="emp_mother_name" /> &nbsp;<?php echo Yii::t('report', 'Mother Name'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_guardian_name" value="emp_guardian_name" /> &nbsp;<?php echo Yii::t('report', 'Father Name'); ?> </label>
	</div>
	
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	     <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_maritalstatus" value="emp_maritalstatus" /> &nbsp;<?php echo Yii::t('report', 'Marital Status'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
		 <label class="checkbox-inline">
		<input type="checkbox"  class="checkbox" name="e_info[]" id="emp_department_name" value="emp_department_name" /> &nbsp;<?php echo Yii::t('report', 'Department'); ?> </label>
	</div>
  </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
 	       <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_designation_name" value="emp_designation_name" /> &nbsp;<?php echo Yii::t('report', 'Designation'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	        <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="city" value="city" /> &nbsp;<?php echo Yii::t('report', 'City'); ?> 			</label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
		 <label class="checkbox-inline">
		<input type="checkbox"  class="checkbox" name="e_info[]" id="emp_reference" value="emp_reference" />&nbsp;<?php echo Yii::t('report', 'Reference'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	        <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_cadd" value="emp_cadd" /> &nbsp;<?php echo Yii::t('report', 'Present Address'); ?> </label>
	</div>
  </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
           <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_padd" value="emp_padd" /> &nbsp;<?php echo Yii::t('report', 'Permanent Address'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_bloodgroup" value="emp_bloodgroup" /> &nbsp;<?php echo Yii::t('report', 'Blood Group'); ?>   </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_gender" value="emp_gender" /> &nbsp;<?php echo Yii::t('report', 'Gender'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_dob", value="emp_dob" />&nbsp;<?php echo Yii::t('report', 'Birth Date'); ?></label>
	</div>
  </div>
</div>


<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_joining_date", value="emp_joining_date" />&nbsp;<?php echo Yii::t('report', 'Joining Date'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_mobile_no", value="emp_mobile_no" />&nbsp;<?php echo Yii::t('report', 'Mobile No'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_guardian_mobile_no" 					value="emp_guardian_mobile_no" /> &nbsp;<?php echo Yii::t('report', 'Guardian Mobile'); ?> </label>
	</div>
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
  	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_email_id" value="emp_email_id" />&nbsp;<?php echo Yii::t('report', 'Email-ID'); ?></label>
	</div>
   </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_bankaccount_no" value="emp_bankaccount_no" />&nbsp;<?php echo Yii::t('report', 'Bank Account No'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_qualification" value="emp_qualification" />&nbsp;<?php echo Yii::t('report', 'Qualification'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_specialization" value="emp_specialization" />&nbsp;<?php echo Yii::t('report', 'Specialization'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_paperpublished" value="emp_paperpublished" />&nbsp;<?php echo Yii::t('report', 'Papers Published'); ?> </label>
	</div>
   </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_bookswritten" value="emp_bookswritten" />&nbsp;<?php echo Yii::t('report', 'Books Written'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_rndprojects" value="emp_rndprojects" />&nbsp;<?php echo Yii::t('report', 'R&D Projects'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_membership" value="emp_membership" />&nbsp;<?php echo Yii::t('report', 'Membership'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_workshops" value="emp_workshops" />&nbsp;<?php echo Yii::t('report', 'Workshops Attended'); ?> </label>
	</div>
   </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_national_conferences" value="emp_national_conferences" />&nbsp;<?php echo Yii::t('report', 'National Conferences'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_international_conferences" value="emp_international_conferences" />&nbsp;<?php echo Yii::t('report', 'International Conferences'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_aadhaar" value="emp_aadhaar" />&nbsp;<?php echo Yii::t('report', 'Aadhaar'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_pan" value="emp_pan" />&nbsp;<?php echo Yii::t('report', 'PAN No.'); ?> </label>
	</div>
	
   </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_voter" value="emp_voter" />&nbsp;<?php echo Yii::t('report', 'Voter ID'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_bankname" value="emp_bankname" />&nbsp;<?php echo Yii::t('report', 'Bank Name'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_ifsc" value="emp_ifsc" />&nbsp;<?php echo Yii::t('report', 'IFSC Code'); ?> </label>
	</div>
		<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox" name="e_info[]" id="emp_attendance_card_id" value="emp_attendance_card_id" /> &nbsp;<?php echo Yii::t('report', 'Attendence Card'); ?> </label>
	</div>
	
   </div>
</div>

<div class="checkbox">
  <div class="col-xs-12 col-lg-12 col-sm-12">
	<div class = "col-sm-3 col-lg-3 col-xs-12" style="padding:3px;">
	  <label class="checkbox-inline">
		<input type="checkbox" class="checkbox"  name="e_info[]" id="emp_cast" value="emp_cast" />&nbsp;<?php echo Yii::t('report', 'Caste'); ?> </label>
	</div>
	
	
   </div>
</div>
</fieldset>
