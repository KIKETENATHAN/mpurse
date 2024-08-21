<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Payment Settings')); ?>

<?php $__env->stopSection(); ?>
<?php
    $settings=settings();
?>
<?php $__env->startSection('breadcrumb'); ?>
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('dashboard')); ?>"><h1><?php echo e(__('Dashboard')); ?></h1></a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#"><?php echo e(__('Payment Settings')); ?></a>
        </li>
    </ul>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php echo e(Form::model($settings, array('route' => array('setting.payment'), 'method' => 'post'))); ?>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('currency_symbol',__('Currency Icon'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('currency_symbol',$settings['CURRENCY_SYMBOL'],array('class'=>'form-control','placeholder'=>__('Enter currency icon'),'required'))); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('currency',__('Currency Code'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('currency',$settings['CURRENCY'],array('class'=>'form-control font-style','placeholder'=>__('Enter currency code'),'required'))); ?>

                        </div>
                    </div>
                    <hr>

                    
                    <div class="row mt-2">
                        <div class="col-auto">
                            <?php echo e(Form::label('stripe_payment',__('Stripe Payment'),array('class'=>'form-label'))); ?>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check custom-chek">
                                    <input class="form-check-input" type="checkbox" name="stripe_payment" id="stripe_payment" <?php echo e($settings['STRIPE_PAYMENT'] == 'on' ? 'checked' : ''); ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('stripe_key',__('Account Key'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('stripe_key',$settings['STRIPE_KEY'],['class'=>'form-control','placeholder'=>__('Enter stripe key')])); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('stripe_secret',__('Account Secret Key'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('stripe_secret',$settings['STRIPE_SECRET'],['class'=>'form-control ','placeholder'=>__('Enter stripe secret')])); ?>

                        </div>
                    </div>
                    <hr>
                    
                    <div class="row mt-2">
                        <div class="col-auto">
                            <?php echo e(Form::label('paypal_payment',__('Paypal Payment'),array('class'=>'form-label'))); ?>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check custom-chek">
                                    <input class="form-check-input" type="checkbox" name="paypal_payment"
                                           id="paypal_payment" <?php echo e($settings['paypal_payment'] == 'on' ? 'checked' : ''); ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <?php echo e(Form::label('paypal_mode',__('Account Mode'),array('class'=>'form-label me-2'))); ?>

                            <div class="form-check custom-chek form-check-inline">
                                <input class="form-check-input" type="radio" value="sandbox" id="sandbox" name="paypal_mode" <?php echo e($settings['paypal_mode'] == 'sandbox' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="sandbox"><?php echo e(__('Sandbox')); ?> </label>
                            </div>
                            <div class="form-check custom-chek form-check-inline">
                                <input class="form-check-input" type="radio" value="live" id="live"
                                       name="paypal_mode" <?php echo e($settings['paypal_mode'] == 'live' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="live"><?php echo e(__('Live')); ?> </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('paypal_client_id',__('Account Client ID'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('paypal_client_id',$settings['paypal_client_id'],['class'=>'form-control','placeholder'=>__('Enter client id')])); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('paypal_secret_key',__('Account Secret Key'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('paypal_secret_key',$settings['paypal_secret_key'],['class'=>'form-control ','placeholder'=>__('Enter secret key')])); ?>

                        </div>
                    </div>
                    <hr>
                    
                    <div class="row mt-2">
                        <div class="col-auto">
                            <?php echo e(Form::label('bank_transfer_payment',__('Bank Transfer Payment'),array('class'=>'form-label'))); ?>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check custom-chek">
                                    <input class="form-check-input" type="checkbox" name="bank_transfer_payment" id="bank_transfer_payment" <?php echo e($settings['bank_transfer_payment'] == 'on' ? 'checked' : ''); ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('bank_name',__('Bank Name'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('bank_name',$settings['bank_name'],['class'=>'form-control','placeholder'=>__('Enter bank name')])); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('bank_holder_name',__('Bank Holder Name'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('bank_holder_name',$settings['bank_holder_name'],['class'=>'form-control','placeholder'=>__('Enter bank holder name')])); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('bank_account_number',__('Bank Account Number'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('bank_account_number',$settings['bank_account_number'],['class'=>'form-control','placeholder'=>__('Enter bank account number')])); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('bank_ifsc_code',__('Bank IFSC'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::text('bank_ifsc_code',$settings['bank_ifsc_code'],['class'=>'form-control','placeholder'=>__('Enter bank ifsc code')])); ?>

                        </div>
                        <div class="form-group col-md-6">
                            <?php echo e(Form::label('bank_other_details',__('Other Details'),array('class'=>'form-label'))); ?>

                            <?php echo e(Form::textarea('bank_other_details',$settings['bank_other_details'],['class'=>'form-control','rows'=>1,'placeholder'=>__('Enter bank other details')])); ?>

                        </div>
                    </div>

                    <div class="text-right">
                        <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-primary btn-rounded'))); ?>

                    </div>
                    <?php echo e(Form::close()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mpurseco/public_html/vms/resources/views/settings/payment.blade.php ENDPATH**/ ?>