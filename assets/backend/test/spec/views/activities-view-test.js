(function() {

  describe('Activities View', function() {
    return describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.UsersView.should.not.be.undefined;
      });
    });
  });

}).call(this);
