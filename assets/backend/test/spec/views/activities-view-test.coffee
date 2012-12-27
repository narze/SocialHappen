describe 'Activities View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.ActivitiesView.should.not.be.undefined