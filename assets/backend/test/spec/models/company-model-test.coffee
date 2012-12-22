describe 'init company model', ->
  it 'should be loaded', ->
    window.backend.Models.CompanyModel.should.not.be.undefined

  it 'should be a model', ->
    window.backend.Models.CompanyModel.should.be.a 'function'
    CompanyModel = new window.backend.Models.CompanyModel
    CompanyModel.should.be.a('object')
      .and.have.property('attributes')
