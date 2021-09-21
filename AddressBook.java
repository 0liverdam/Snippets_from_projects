package BFST19_GroupP;

import java.util.*;

public class AddressBook {
                                                            //This is done so the whole map doesn't care about case when comparing
    private static TreeMap<String, HashSet<String>> streetToCities = new TreeMap<>(String.CASE_INSENSITIVE_ORDER);
    private static TreeMap<String, HashSet<String>> cityToStreets = new TreeMap<>(String.CASE_INSENSITIVE_ORDER);
    private static List<Address> addresses = new ArrayList<>();
    private static boolean isAddressesSorted = false;

    /**
     * add a new address made of the parameters to the list and maps of addresses.
     * @param city a String denoting the city's name
     * @param street a String denoting the street's name
     * @param houseNumber a string denoting the house number
     * @param coord an OSMNode giving the map location of the address
     */
    public static void addAddress(String city, String street, String houseNumber, OSMNode coord){
        addresses.add(new Address(city,street,houseNumber,coord));
        if(city!=null && street!=null){
            HashSet<String> cities;
            if(streetToCities.containsKey(street)){
                cities = streetToCities.get(street);
            } else {
                cities = new HashSet<>();
            }
            cities.add(city);
            streetToCities.put(street,cities);

            HashSet<String> streets;
            if (cityToStreets.containsKey(city)){
                streets = cityToStreets.get(city);
            }else {
                streets = new HashSet<>();
            }
            streets.add(street);
            cityToStreets.put(city, streets);
        }
        isAddressesSorted = false;
    }

    public static List<String> getCitiesFromStreet(String street){
        HashSet<String> cities = streetToCities.get(street);
        if (cities == null) return new ArrayList<>();
        return new ArrayList<>(cities);
    }

    public static List<String> getStreetsFromCity(String city) {
        HashSet<String> streets = cityToStreets.get(city);
        return new ArrayList<>(streets);
    }

    public static Map<String, HashSet<String>> getCitiesFromPrefix(String prefix){
        return cityToStreets.subMap(prefix, prefix + Character.MAX_VALUE);
    }

    public static Map<String, HashSet<String>> getStreetsFromPrefix(String prefix){
        return streetToCities.subMap(prefix, prefix + Character.MAX_VALUE);
    }

    public static boolean containsCity(String city) {
        return cityToStreets.containsKey(city);
    }

    public static boolean containsStreet(String street) {
        return streetToCities.containsKey(street);
    }

    /**
     * returns an existing address from a string formatted "street housenumber city"
     * This method returns null if the string query can't be parsed into an existing address
     * @param query a String used to find the address formatted "street housenumber city"
     * @return the Address found from the string query
     */
    public static Address getAddress(String query){
        Address searchAddress = AddressParser.parse(query);
        if (!isAddressesSorted) {
            Collections.sort(addresses);
            isAddressesSorted = true;
        }
        int index = Collections.binarySearch(addresses, searchAddress);
        Address address;
        try{
            address = addresses.get(index);
        } catch (Exception e){
            address = null;
        }
        return address;
    }

    public static TreeMap<String, HashSet<String>> getStreetToCities() {
        return streetToCities;
    }

    public static TreeMap<String, HashSet<String>> getCityToStreets() {
        return cityToStreets;
    }

    public static List<Address> getAddresses() {
        return addresses;
    }

    public static void setStreetToCities(TreeMap<String, HashSet<String>> streetToCities) {
        AddressBook.streetToCities = streetToCities;
    }

    public static void setCityToStreets(TreeMap<String, HashSet<String>> cityToStreets) {
        AddressBook.cityToStreets = cityToStreets;
    }

    public static void setAddresses(List<Address> addresses) {
        AddressBook.addresses = addresses;
        isAddressesSorted = false;
    }
}
