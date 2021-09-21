package BFST19_GroupP;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

public class KdTree implements Serializable {
    private KdTreeNode rootNode;
    private final int MAX_LEAF_SIZE = 100;
    private double smallestDistance = 0.0;
    private int startIndexOfNearestDrawableCoord;
    private BoundingBox currentSearchRange = null;

    /**
     * This class takes a list of Drawables and builds a kd-Tree out of them.
     * @param drawables a List of Drawables used to build the kd-Tree
     */
    public KdTree(List<Drawable> drawables) {
        rootNode = buildTree(drawables, 0);
    }

    private KdTreeNode buildTree(List<Drawable> drawables, int depth) {
        List<Drawable> drawables1 = new ArrayList<>();
        List<Drawable> drawables2 = new ArrayList<>();
        float firstSplitLine, secondSplitLine;
        if (drawables.size() <= MAX_LEAF_SIZE) {
            return new KdTreeNode(drawables);
        } else if (depth % 2 == 0) {
            firstSplitLine = findMedianX(drawables);
            secondSplitLine = firstSplitLine;
            for (Drawable drawable : drawables) {
                if (drawable.getBoundingBox().getMaxX() <= firstSplitLine) {
                    drawables1.add(drawable);
                } else {
                    drawables2.add(drawable);
                    secondSplitLine = Math.min(secondSplitLine, drawable.getBoundingBox().getMinX());
                }
            }
        } else {
            firstSplitLine = findMedianY(drawables);
            secondSplitLine = firstSplitLine;
            for (Drawable drawable : drawables) {
                if (drawable.getBoundingBox().getMaxY() <= firstSplitLine) {
                    drawables1.add(drawable);
                } else {
                    drawables2.add(drawable);
                    secondSplitLine = Math.min(secondSplitLine, drawable.getBoundingBox().getMinY());
                }
            }
        }
        KdTreeNode leftSubTree = buildTree(drawables1, depth + 1);
        KdTreeNode rightSubTree = buildTree(drawables2, depth + 1);
        return new KdTreeNode(firstSplitLine, secondSplitLine, leftSubTree, rightSubTree);
    }

    private float findMedianY(List<Drawable> drawables) {
        Collections.sort(drawables, new DrawableLatComparator());
        if (drawables.size() % 2 == 0) {
            return (drawables.get(drawables.size() / 2 - 1).getBoundingBox().getMaxY() + drawables.get(drawables.size() / 2).getBoundingBox().getMaxY()) / 2;
        } else {
            return drawables.get(drawables.size() / 2).getBoundingBox().getMaxY();
        }
    }

    private float findMedianX(List<Drawable> drawables) {
        Collections.sort(drawables, new DrawableLonComparator());
        if (drawables.size() % 2 == 0) {
            return (drawables.get(drawables.size() / 2 - 1).getBoundingBox().getMaxX() + drawables.get(drawables.size() / 2).getBoundingBox().getMaxX()) / 2;
        } else {
            return drawables.get(drawables.size() / 2).getBoundingBox().getMaxX();
        }
    }

    /**
     * This method takes a Boundingbox and returns an ArrayList of all Drawables inside the Boundingbox
     * The List is empty if there are no Drawables inside the Boundingbox
     * @param searchRange   a Boundingbox giving the searchRange
     * @return All Drawables in the Boundingbox in form of an ArrayList
     */
    public List<Drawable> searchKdTree(BoundingBox searchRange) {
        return searchKdTree(rootNode, searchRange, 0);
    }

    private List<Drawable> searchKdTree(KdTreeNode node, BoundingBox searchRange, int depth) {
        ArrayList<Drawable> reportedDrawables = new ArrayList<>();
        if (node.getData() != null) {
            List<Drawable> nodeData = node.getData();
            for (Drawable drawable : nodeData) {
                if (drawable.getBoundingBox().overLap(searchRange)) reportedDrawables.add(drawable);
            }
            return reportedDrawables;
        } else if (depth % 2 == 0) { //Vertical split line
            if (searchRange.getMinX() < node.getFirstSplitLine())
                reportedDrawables.addAll(searchKdTree(node.getLeft(), searchRange, depth + 1));
            if (searchRange.getMaxX() > node.getSecondSplitLine())
                reportedDrawables.addAll(searchKdTree(node.getRight(), searchRange, depth + 1));
        } else if (depth % 2 != 0) { //Horizontal split line
            if (searchRange.getMinY() < node.getFirstSplitLine())
                reportedDrawables.addAll(searchKdTree(node.getLeft(), searchRange, depth + 1));
            if (searchRange.getMaxY() > node.getSecondSplitLine())
                reportedDrawables.addAll(searchKdTree(node.getRight(), searchRange, depth + 1));
        }
        return reportedDrawables;
    }

    public KdTreeNode getRootNode() {
        return rootNode;
    }

    private double distanceBetween(double x1, double y1, double x2, double y2) {
        return Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
    }

    private boolean isThereRiskOfEdgeCases() {
        float usedSearchRadius = (currentSearchRange.getMaxX()-currentSearchRange.getMinX())/2;
        return smallestDistance > usedSearchRadius;
    }

    /**
     * This method takes a longitude and latitude and finds the nearest Drawable and returns the nearest point in that
     * Drawable in the form of a CoordSet.
     * @param lon  a float specifying the longitude
     * @param lat  a float specifying the latitude
     * @return a CoordSet with the same longitude and latitude as the nearest point
     */
    public CoordSet nearestNeightbourCoord(float lon, float lat) {
        currentSearchRange = new BoundingBox(lon, lon, lat, lat);
        smallestDistance = 0.0;
        return findClosestCoordInDrawables(expandingKdTreeSearch(null), new CoordSet(lon, lat));
    }

    private void increaseSearchRange(float extraRange) {
        currentSearchRange = new BoundingBox(currentSearchRange.getMinX() - extraRange, currentSearchRange.getMaxX() + extraRange, currentSearchRange.getMinY() - extraRange, currentSearchRange.getMaxY() + extraRange);
    }

    private List<Drawable> expandingKdTreeSearch(List<Drawable> drawables) {
        List<Drawable> result;
        if (drawables == null || drawables.size() == 0) {
            increaseSearchRange(0.05f);
            result = expandingKdTreeSearch(searchKdTree(currentSearchRange));
        } else {
            result = drawables;
        }

        return result;
    }

    private CoordSet findClosestCoordInDrawables(List<Drawable> drawables, CoordSet point) {
        Drawable nearestDrawable = null;
        for (Drawable drawable : drawables) {
            boolean isNearest = false;
            for (int i = 0; i < drawable.getCoords().length; i += 2) {
                float x1 = drawable.getCoords()[i];
                float y1 = drawable.getCoords()[i + 1];
                if (smallestDistance != 0.0) {
                    if (smallestDistance > distanceBetween((double) x1, (double) y1, (double) point.getLon(), (double) point.getLat())) {
                        smallestDistance = distanceBetween((double) x1, (double) y1, (double) point.getLon(), (double) point.getLat());
                        startIndexOfNearestDrawableCoord = i;
                        isNearest = true;
                    }
                } else {
                    smallestDistance = distanceBetween((double) x1, (double) y1, (double) point.getLon(), (double) point.getLat());
                    startIndexOfNearestDrawableCoord = i;
                    isNearest = true;
                }
            }
            if (isNearest) {
                nearestDrawable = drawable;
            }
        }
        if (isThereRiskOfEdgeCases()) {
            increaseSearchRange(0.05f);
            resetSmallestDistance();
            return findClosestCoordInDrawables(expandingKdTreeSearch(drawables), point);
        }
        return new CoordSet(nearestDrawable.getCoords()[startIndexOfNearestDrawableCoord], nearestDrawable.getCoords()[startIndexOfNearestDrawableCoord + 1]);
    }

    public double getSmallestDistance() {
        return smallestDistance;
    }

    public void resetSmallestDistance() {
        smallestDistance = 0.0;
    }
}

