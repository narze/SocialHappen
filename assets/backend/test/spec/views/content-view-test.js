(function() {

  describe('Content View', function() {
    return describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.ContentView.should.not.be.undefined;
      });
    });
  });

}).call(this);
