describe 'NavBar View', ->
  describe 'view is loaded', ->
    it 'should initialized view', ->
      window.backend.Views.NavBarView.should.not.be.undefined
    it 'should render template', ->
      $('#app > #navbar-view').text().should.not.be.empty

  describe 'Content', ->
    it 'should have SocialHappen as header text', ->
      $('#navbar-view .hidden-phone').text().should.match(/SocialHappen/)