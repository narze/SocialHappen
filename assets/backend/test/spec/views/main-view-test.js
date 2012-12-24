(function() {

  describe('Main View', function() {
    return describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.MainView.should.not.be.undefined;
      });
    });
  });

}).call(this);
