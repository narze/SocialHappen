(function() {

  describe('scripts are loaded', function() {
    describe('require.js is loaded', function() {
      return it('should have require function', function() {
        return require.should.be.a('function');
      });
    });
    describe('main is loaded', function() {
      return it('should have window.mainLoaded', function() {
        return expect(window.mainLoaded).to.equal(true);
      });
    });
    return describe('app is loaded', function() {
      return it('should have window.appLoaded', function() {
        return expect(window.appLoaded).to.equal(true);
      });
    });
  });

}).call(this);
