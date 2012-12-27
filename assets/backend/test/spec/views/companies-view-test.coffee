describe 'Companies View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.CompaniesView.should.not.be.undefined