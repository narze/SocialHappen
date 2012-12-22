(function() {

  describe('init company collection', function() {
    return it('should be loaded', function() {
      return window.backend.Collections.CompanyCollection.should.be.a('function');
    });
  });

}).call(this);
