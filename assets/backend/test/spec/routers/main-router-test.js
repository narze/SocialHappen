(function() {

  describe('Main Router', function() {
    describe('router is loaded', function() {
      return it('should initialized router', function() {
        return window.backend.Routers.MainRouter.should.not.be.undefined;
      });
    });
    describe('routes', function() {
      it('should have default (blank) route', function() {
        return window.backend.Routers.MainRouter.routes[''].should.not.be.undefined;
      });
      it('should have users route', function() {
        return window.backend.Routers.MainRouter.routes['users'].should.be.equal('users');
      });
      return it('should have activities route', function() {
        return window.backend.Routers.MainRouter.routes['activities'].should.be.equal('activities');
      });
    });
    return describe('routing', function() {
      it('should not load any views when switched to a bad route', function() {
        window.backend.Routers.MainRouter.navigate('somebadroute', {
          trigger: true
        });
        return window.backend.Routers.MainRouter.notFound.should.be["true"];
      });
      it('should load users view when switched route to users', function() {
        window.backend.Routers.MainRouter.navigate('users', {
          trigger: true
        });
        return window.backend.Views.UsersView.rendered.should.be["true"];
      });
      return it('should load activities view when switched route to activities', function() {
        window.backend.Routers.MainRouter.navigate('activities', {
          trigger: true
        });
        return window.backend.Views.UsersView.rendered.should.be["true"];
      });
    });
  });

}).call(this);
