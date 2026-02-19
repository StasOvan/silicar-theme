<footer class="site-footer">
    <div class="container">
        <div class="footer-row" style="display: flex; align-items: center; justify-content: space-between;">
            
            <div class="footer-info">
                <p>&copy; <?php echo date('Y'); ?> <?php echo myshop_get_owner(); ?><br>ИНН <?php echo myshop_get_inn(); ?></p>
                <a href="<?php echo esc_url( myshop_get_privacy_page_url() ); ?>">Политика конфиденциальности</a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>