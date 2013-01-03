(function() {

  describe('Company model', function() {
    it('should be loaded', function() {
      return window.backend.Models.CompanyModel.should.not.be.undefined;
    });
    return it('should be a model', function() {
      var CompanyModel;
      window.backend.Models.CompanyModel.should.be.a('function');
      CompanyModel = new window.backend.Models.CompanyModel;
      CompanyModel.should.be.a('object').and.have.property('attributes');
      return CompanyModel.should.have.property('id');
    });
  });

}).call(this);
