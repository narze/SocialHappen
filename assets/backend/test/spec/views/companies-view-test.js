(function() {

  describe('Companies View', function() {
    return describe('view is loaded', function() {
      return it('should initialized view', function() {
        return window.backend.Views.CompaniesView.should.not.be.undefined;
      });
    });
  });

}).call(this);
