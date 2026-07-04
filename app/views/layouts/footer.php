<?php if (Auth::check()): ?>
        </div><!-- end page-content -->

        <footer class="footer">
            <p>&copy; <?= date('Y') ?> QR Attendance System &mdash; Iqra University</p>
        </footer>

    </div><!-- end main -->

</div><!-- end wrapper -->

<?php else: ?>
</div><!-- end auth-wrapper -->
<?php endif; ?>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>