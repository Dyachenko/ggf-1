import Ember from 'ember';
import AuthenticatedRouteMixin from 'simple-auth/mixins/authenticated-route-mixin';

const {
  RSVP
  } = Ember;

export default Ember.Route.extend(AuthenticatedRouteMixin, {

  model: function () {
    const store = this.store;
    const tournamentId = this.modelFor('tournament').get('id');

    return RSVP.hash({
      tournament: store.peekRecord('tournament', tournamentId, function (tournament) {
        return !tournament.get('isDirty');
      })
    });
  },

  actions: {
    save (params) {
      const flashMessages = Ember.get(this, 'flashMessages');
      const store = this.store;

      return new Ember.RSVP.Promise((resolve, reject) => {
        store.find('tournament', this.currentModel.tournament.get('id')).then((tournament) => {

          if (tournament.get('isStarted') && tournament.get('teams').length < 2) {
            return flashMessages.danger('Tournament should have at least 2 teams.');
          }

          // update `oneWay` binded attributes
          tournament.set('name', params.name);
          tournament.set('description', params.description);

          tournament.validate().then(() => {
            tournament.save()
              .then(() => {
                resolve();
                flashMessages.success('Tournament has been saved');

              })
              .catch((err) => {
                tournament.rollbackAttributes();

                flashMessages.danger('Unable to save tournament');

                reject(err);
              });
          }).catch(() => {

          });
        });
      });
    }
  },
  deactivate: function() {
    this.currentModel.tournament.rollbackAttributes();
  },
});
