describe 'Activities View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.UsersView.should.not.be.undefined