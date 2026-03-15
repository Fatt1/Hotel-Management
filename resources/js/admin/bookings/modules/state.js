/**
 * Shared mutable state for booking pages.
 * ES modules are singletons — all importers share this same object.
 */
export const state = {
    selectedDates: [],
    selectedRooms: [],           // room objects from API
    currentRoomTypeFilter: '',
    currentCustomer: null,       // null | 'new' | customer object

    // { [roomId]: { [serviceId]: { id, name, unit_price, unit, group, quantity } } }
    roomServices: {},
};
