(function() {

  describe('Company collection', function() {
    it('should be loaded', function() {
      return window.backend.Collections.CompanyCollection.should.not.be.undefined;
    });
    it('should be a collection', function() {
      return window.backend.Collections.CompanyCollection.should.be.an('object').and.have.property('models')["with"].length(0);
    });
    return it('should have company model inside it', function() {
      return window.backend.Collections.CompanyCollection.model.should.equal(window.backend.Models.CompanyModel);
    });
  });

}).call(this);
