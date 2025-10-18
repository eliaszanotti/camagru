<?php
// FormService system only - all legacy support removed
if (isset($formService) && $formService instanceof FormService): ?>
    <!-- FormService - Errors -->
    <?php if ($formService->hasErrors()): ?>
        <div class="alert alert-error">
            <ul class="list-disc list-inside">
                <?php foreach ($formService->getErrors() as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- FormService - Success -->
    <?php if ($formService->hasSuccess()): ?>
        <div class="alert alert-success">
            <!-- TODO mettre lucide -->
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($formService->getSuccess()); ?></span>
        </div>
    <?php endif; ?>
<?php endif; ?>