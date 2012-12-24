(function() {

  describe('NavBar View', function() {
    return describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.NavBarView.should.not.be.undefined;
      });
    });
  });

}).call(this);
