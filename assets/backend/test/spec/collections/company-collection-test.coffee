describe 'init company collection', ->
  it 'should be loaded', ->
    window.backend.Collections.CompanyCollection.should.be.a 'function'