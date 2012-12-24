(function() {

  describe('Content View', function() {
    return describe('view is loaded', function() {
      it('should initialized view', function() {
        return window.backend.Views.ContentView.should.not.be.undefined;
      });
      return it('should render template', function() {
        return $('#app #content-view').text().should.not.be.empty;
      });
    });
  });

}).call(this);
