        </div>
    </main>
</div>
<script src="/assets/app.js"></script>
<?php if (!empty($extraScripts) && is_array($extraScripts)): ?>
    <?php foreach ($extraScripts as $scriptPath): ?>
        <script src="<?php echo htmlspecialchars($scriptPath, ENT_QUOTES); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
