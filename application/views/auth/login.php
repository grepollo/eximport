<div class="uk-vertical-align uk-text-center uk-height-1-1" style="padding-top:50px">
    <div class="uk-vertical-align-middle" style="width: 250px;">
<!--h1><?php echo lang('login_heading');?></h1-->
<p><?php echo lang('login_subheading');?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/login", array('class' => 'uk-panel uk-panel-box uk-form'));?>
<div class="uk-form-row">    
    <!--label class="uk-form-label" for="identity"><?php echo lang('login_identity_label');?></label-->
    <div class="uk-form-controls">
       <?php echo form_input($identity);?> 
    </div>
</div>

<div class="uk-form-row">   
    <!--label class="uk-form-label" for="password"><?php echo lang('login_password_label');?></label-->
    <div class="uk-form-controls">
      <?php echo form_input($password);?>
    </div>
</div>

<!--div class="uk-form-row">
    <label class="uk-form-label" for="remember"><?php //echo lang('login_remember_label');?></label>
    <div class="uk-form-controls">
       <?php //echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
    </div>
</div-->
<div class="uk-form-row">
    <input type="submit" name="submit" value="Login" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" />
</div>
        <div class="uk-form-row uk-text-small">
            <label class="uk-float-left">
                <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?> Remember Me
            </label>
            <a class="uk-float-right uk-link uk-link-muted" href="forgot_password">Forgot Password?</a>
        </div>
<?php echo form_close();?>
    </div>
</div>