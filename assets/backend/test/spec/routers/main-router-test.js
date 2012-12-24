(function() {

  describe('Main Router', function() {
    return describe('router is loaded', function() {
      return it('should initialized router', function() {
        return window.backend.Routers.MainRouter.should.not.be.undefined;
      });
    });
  });

}).call(this);
