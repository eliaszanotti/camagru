<?php
// Support both old system ($errors, $success) and new FormService
if (isset($formService) && $formService instanceof FormService): ?>
    <!-- New FormService system - Errors -->
    <?php if ($formService->hasErrors()): ?>
        <div class="alert alert-error">
            <ul class="list-disc list-inside">
                <?php foreach ($formService->getErrors() as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- New FormService system - Success -->
    <?php if ($formService->hasSuccess()): ?>
        <div class="alert alert-success">
            <!-- TODO mettre lucide -->
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($formService->getSuccess()); ?></span>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Legacy system - Errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php if (isset($errors['general'])): ?>
                <!-- Single general error -->
                <span><?php echo htmlspecialchars($errors['general']); ?></span>
            <?php else: ?>
                <!-- Multiple errors in list -->
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Legacy system - Success -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <!-- TODO mettre lucide -->
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>
<?php endif; ?>