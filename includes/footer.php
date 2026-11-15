  </main>

  <footer class="site-footer">
    <div class="wrap">
      <div class="footer-top">
        <div>
          <p class="footer-tagline"><?= e($brand['tagline'] ?? 'Ideas, built into software.') ?></p>
          <p class="footer-loc"><?= e($brand['location'] ?? '') ?></p>
        </div>
        <a class="btn btn-primary" href="contact">Start a project</a>
      </div>
      <div class="footer-cols">
        <div>
          <h5>Pages</h5>
          <ul>
            <li><a href="work">Work</a></li>
            <li><a href="services">Services</a></li>
            <li><a href="about">About</a></li>
            <li><a href="contact">Contact</a></li>
          </ul>
        </div>
        <div>
          <h5>Elsewhere</h5>
          <ul>
            <li><a href="<?= e($brand['linkedin'] ?? '#') ?>" rel="noopener">LinkedIn</a></li>
            <li><a href="<?= e($brand['github'] ?? '#') ?>" rel="noopener">GitHub</a></li>
            <li><a href="<?= e($brand['twitter'] ?? '#') ?>" rel="noopener">X / Twitter</a></li>
          </ul>
        </div>
        <div>
          <h5>Contact</h5>
          <ul>
            <li><a href="mailto:<?= e($brand['email'] ?? '') ?>"><?= e($brand['email'] ?? '') ?></a></li>
            <li><a href="contact">Book a call</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© <span id="year"><?= date('Y') ?></span> <?= e($brand['name'] ?? 'Nexabie') ?>. All rights reserved.</span>
        <span class="footer-legal"><a href="privacy">Privacy</a><a href="#top">Back to top</a></span>
      </div>
    </div>
  </footer>

  <style>.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}</style>
  <script src="js/main.js"></script>
</body>
</html>
