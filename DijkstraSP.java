package BFST19_GroupP;

import java.util.ArrayList;

public class DijkstraSP {
    private Edge[] edgeTo;
    private double[] distanceTo;
    private IndexMinPQ<Double> pq;
    private RoadGraph graph;
    private VehicleType transportMethod;
    private int startIndex;
    private int endIndex;

    /**
     * Searches a graph for the shortest path between 2 points in the graph
     * @param graph the RoadGraph that is searched through
     * @param startIndex the index of the start Vertex in the graph
     * @param endIndex the index of the end Vertex in the graph
     * @param transportMethod a VehicleType used to define which edges can be used in the graph
     */
    public DijkstraSP(RoadGraph graph, int startIndex, int endIndex, VehicleType transportMethod) {
        this.startIndex = startIndex;
        this.endIndex = endIndex;
        this.graph = graph;
        this.transportMethod = transportMethod;
        searchWithoutHeuristics();
    }

    /**
     * Searches the graph using dijkstra's algorithm without heuristics.
     */
    private void searchWithoutHeuristics() {
        edgeTo = new Edge[graph.getAllVertices().size()];
        distanceTo = new double[graph.getAllVertices().size()];
        pq = new IndexMinPQ<>(graph.getAllVertices().size());

        for (int i = 0; i < distanceTo.length; i++) {
            distanceTo[i] = Double.POSITIVE_INFINITY;
        }
        distanceTo[startIndex] = 0.0;

        pq.insert(startIndex, 0.0);
        while (!pq.isEmpty()) {
            int currentIndex = pq.delMin();
            relax(currentIndex);
            if (currentIndex == endIndex) {
                return;
            }
        }
    }

    private void relax(int vertexIndex) {
        for (Edge edge : graph.getVertex(vertexIndex).getEdges()) {
            if (edge.canIGoThisWay(vertexIndex, transportMethod)) {
                int nextVertexIndex = edge.getNextVertex(vertexIndex);
                if (distanceTo[nextVertexIndex] > distanceTo[vertexIndex] + edge.getWeight(transportMethod)) {
                    distanceTo[nextVertexIndex] = distanceTo[vertexIndex] + edge.getWeight(transportMethod);
                    edgeTo[nextVertexIndex] = edge;
                    if (pq.contains(nextVertexIndex)) {
                        pq.changeKey(nextVertexIndex, distanceTo[nextVertexIndex]);
                    } else {
                        pq.insert(nextVertexIndex, distanceTo[nextVertexIndex]);
                    }
                }
            }
        }
    }

    /**
     * Returns all Edges in the shortest path between the start of the graph and the end index, in the form of an ArrayList.
     * The order of the edges is reversed the index 0 of the ArrayList is the edge going to the end Vertex
     * This will return null if there is no path between the points
     * @param endIndex the index of the end Vertex
     * @return An ArrayList of all the edges between the start and end
     */
    public ArrayList<Edge> pathTo(int endIndex) {
        if (!hasPathTo(endIndex)) {
            return null;
        }
        ArrayList<Edge> path = new ArrayList<>();
        int currentVertexIndex = endIndex;
        for (Edge edge = edgeTo[endIndex]; edge != null; edge = edgeTo[currentVertexIndex]) {
            path.add(edge);
            currentVertexIndex = edge.getNextVertex(currentVertexIndex);
        }
        return path;
    }

    private boolean hasPathTo(int endIndex) {
        return distanceTo[endIndex] < Double.POSITIVE_INFINITY;
    }

    public Edge[] getEdges() {
        return edgeTo;
    }

    public int getStartIndex() {
        return startIndex;
    }
}