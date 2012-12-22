describe 'init company collection', ->
  it 'should be loaded', ->
    window.backend.Collections.CompanyCollection.should.not.be.undefined

  it 'should be a collection', ->
    window.backend.Collections.CompanyCollection.should.be.an('object')
      .and.have.property('models')
      .with.length(0)

  it 'should have company model inside it', ->
    window.backend.Collections.CompanyCollection.model.should.equal window.backend.Models.CompanyModel