(function() {

  describe('Main Router', function() {
    describe('router is loaded', function() {
      return it('should initialized router', function() {
        return window.backend.Routers.SigninRouter.should.not.be.undefined;
      });
    });
    describe('routes', function() {
      return it('should have default (blank) route', function() {
        return window.backend.Routers.SigninRouter.routes[''].should.be.equal('signin');
      });
    });
    return describe('routing', function() {
      it('should not load any views when switched to a bad route', function() {
        window.backend.Routers.SigninRouter.navigate('somebadroute', {
          trigger: true
        });
        return window.backend.Routers.SigninRouter.notFound.should.be["true"];
      });
      describe('signin', function() {
        it('should load signin view when switched route to signin', function() {
          window.backend.Routers.SigninRouter.navigate('devices', {
            trigger: true
          });
          return window.backend.Views.SigninView.rendered.should.equal(true);
        });
        it('should render #signin-view into #app', function() {
          return $('#app').find('#signin-view').length.should.not.equal(0);
        });
        return it('should have all neccessary items', function() {
          $('#app').find('#signin-view').find('input#username').length.should.equal(1);
          $('#app').find('#signin-view').find('input#password').length.should.equal(1);
          $('#app').find('#signin-view').find('[type=submit]').length.should.equal(1);
          return $('#app').find('#signin-view').find('#facebook-connect').length.should.equal(1);
        });
      });
      return describe('change route back', function() {
        return window.backend.Routers.SigninRouter.navigate('', {
          trigger: true
        });
      });
    });
  });

}).call(this);
