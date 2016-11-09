import Ember from 'ember';
import config from './config/environment';

var Router = Ember.Router.extend({
  location: config.locationType
});

Router.map(function() {
  this.resource('tournaments', function() {
    this.route('new');

    this.resource('tournament', {path: '/tournament/:tournamentId'}, function() {
      this.route('standings');
      this.route('teams');
      this.route('team', {path: '/team/:id'});
      this.route('matches');
      this.route('settings');
    });
  });

  this.resource('leagues', function() {
    this.route('new');

    this.resource('league', {path: '/:id'}, function() {
      this.route('teams', function () {
        this.route('new');
      });
    });
  });
});

export default Router;
